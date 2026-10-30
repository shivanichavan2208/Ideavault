
<?php
// ==========================================
// IdeaVault - Admin Dashboard
// File: admin-dashboard.php
// ==========================================

session_start();

// Database connection
$host = "localhost";
$dbname = "ideavault";
$username = "root";
$password = "";

// Create CSRF token
if (empty($_SESSION['admin_csrf'])) {
    $_SESSION['admin_csrf'] = bin2hex(random_bytes(32));
}

$message = "";
$messageType = "";

try {
    $conn = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Handle approve/reject actions
    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        $token = $_POST['csrf_token'] ?? '';
        $projectId = filter_input(INPUT_POST, 'project_id', FILTER_VALIDATE_INT);
        $action = $_POST['action'] ?? '';

        if (
            !is_string($token) ||
            !hash_equals($_SESSION['admin_csrf'], $token)
        ) {
            $message = "Invalid request. Please refresh the page.";
            $messageType = "error";

        } elseif (!$projectId || !in_array($action, ['approve', 'reject'], true)) {
            $message = "Invalid project or action.";
            $messageType = "error";

        } else {

            $newStatus = ($action === "approve") ? "approved" : "rejected";

            // Only pending projects can be updated
            $stmt = $conn->prepare(
                "UPDATE projects
                 SET status = :status
                 WHERE id = :id AND status = 'pending'"
            );

            $stmt->execute([
                ':status' => $newStatus,
                ':id' => $projectId
            ]);

            if ($stmt->rowCount() > 0) {
                $message = "Project successfully " . $newStatus . ".";
                $messageType = "success";
            } else {
                $message = "Project was not found or has already been reviewed.";
                $messageType = "error";
            }
        }
    }

    // Get project statistics
    $totalProjects = $conn->query(
        "SELECT COUNT(*) FROM projects"
    )->fetchColumn();

    $pendingProjects = $conn->query(
        "SELECT COUNT(*) FROM projects WHERE status = 'pending'"
    )->fetchColumn();

    $approvedProjects = $conn->query(
        "SELECT COUNT(*) FROM projects WHERE status = 'approved'"
    )->fetchColumn();

    $rejectedProjects = $conn->query(
        "SELECT COUNT(*) FROM projects WHERE status = 'rejected'"
    )->fetchColumn();

    // Fetch pending projects
    $stmt = $conn->prepare(
        "SELECT id, title, category, description,
                skills_needed, team_size, created_at
         FROM projects
         WHERE status = 'pending'
         ORDER BY created_at DESC"
    );

    $stmt->execute();
    $pendingList = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database error. Please check your database and table structure.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Dashboard | IdeaVault</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #071426, #102b50);
            color: white;
        }

        .layout {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 245px;
            flex-shrink: 0;
            background: rgba(5, 18, 38, 0.96);
            padding: 28px 18px;
            border-right: 1px solid rgba(0, 220, 255, 0.15);
        }

        .logo {
            color: #22d3ee;
            font-size: 25px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 35px;
        }

        .sidebar a {
            display: block;
            color: #c4d9ef;
            text-decoration: none;
            padding: 13px 15px;
            margin-bottom: 9px;
            border-radius: 10px;
            transition: 0.3s;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: linear-gradient(90deg, #087da8, #075985);
            color: white;
            transform: translateX(4px);
        }

        /* Main content */
        .main-content {
            flex: 1;
            min-width: 0;
            padding: 35px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 30px;
        }

        .topbar h1 {
            font-size: 30px;
        }

        .topbar p {
            color: #a9c6e5;
            margin-top: 8px;
        }

        .admin-label {
            background: rgba(6, 182, 212, 0.15);
            color: #67e8f9;
            border: 1px solid rgba(34, 211, 238, 0.3);
            padding: 10px 16px;
            border-radius: 20px;
            font-size: 13px;
        }

        /* Alert message */
        .alert {
            padding: 14px 18px;
            border-radius: 10px;
            margin-bottom: 22px;
        }

        .success {
            background: rgba(34, 197, 94, 0.13);
            color: #86efac;
            border: 1px solid rgba(34, 197, 94, 0.35);
        }

        .error {
            background: rgba(239, 68, 68, 0.13);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.35);
        }

        /* Statistics */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(140px, 1fr));
            gap: 20px;
            margin-bottom: 35px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(34, 211, 238, 0.2);
            border-radius: 15px;
            padding: 24px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .stat-card h3 {
            color: #a9c6e5;
            font-size: 14px;
            margin-bottom: 14px;
        }

        .stat-card .number {
            color: #22d3ee;
            font-size: 32px;
            font-weight: bold;
        }

        /* Pending section */
        .section-title {
            font-size: 23px;
            margin-bottom: 20px;
        }

        .project-list {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
        }

        .project-card {
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(34, 211, 238, 0.18);
            border-radius: 15px;
            padding: 23px;
            transition: 0.3s;
        }

        .project-card:hover {
            border-color: #22d3ee;
            transform: translateY(-3px);
        }

        .project-card h3 {
            font-size: 20px;
            margin-bottom: 12px;
        }

        .category {
            display: inline-block;
            background: rgba(6, 182, 212, 0.15);
            color: #67e8f9;
            padding: 6px 11px;
            border-radius: 20px;
            font-size: 12px;
            margin-bottom: 15px;
        }

        .description {
            color: #c4d9ef;
            line-height: 1.7;
            margin-bottom: 15px;
            overflow-wrap: anywhere;
        }

        .project-info {
            color: #a9c6e5;
            font-size: 13px;
            line-height: 1.9;
            margin-bottom: 18px;
            overflow-wrap: anywhere;
        }

        .status {
            display: inline-block;
            color: #fcd34d;
            background: rgba(245, 158, 11, 0.12);
            padding: 6px 11px;
            border-radius: 20px;
            font-size: 12px;
            margin-bottom: 15px;
        }

        .action-buttons {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .action-buttons button {
            border: none;
            padding: 10px 17px;
            border-radius: 8px;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        .approve-btn {
            background: #15803d;
        }

        .approve-btn:hover {
            background: #16a34a;
        }

        .reject-btn {
            background: #b91c1c;
        }

        .reject-btn:hover {
            background: #dc2626;
        }

        .empty-message {
            background: rgba(255, 255, 255, 0.06);
            border-radius: 12px;
            padding: 25px;
            color: #b8cee5;
            line-height: 1.7;
        }

        .footer {
            text-align: center;
            color: #7896b5;
            margin-top: 40px;
            font-size: 13px;
        }

        @media (max-width: 950px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .project-list {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 650px) {
            .layout {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
            }

            .main-content {
                padding: 22px 16px;
            }

            .topbar h1 {
                font-size: 25px;
            }
        }
    </style>
</head>

<body>

<div class="layout">

    <!-- Sidebar -->
    <aside class="sidebar">

        <div class="logo">IdeaVault</div>

        <a href="dashboard.html">🏠 Dashboard</a>
        <a href="explore-ideas.php">💡 Explore Ideas</a>
        <a href="submit-idea.php">➕ Submit Idea</a>
        <a href="find-team.php">👥 Find Team</a>
        <a href="my-projects.php">📁 My Projects</a>
        <a href="project-dashboard.php">📊 Project Dashboard</a>
        <a href="admin-dashboard.php" class="active">⚙️ Admin Dashboard</a>

    </aside>

    <!-- Main content -->
    <main class="main-content">

        <div class="topbar">
            <div>
                <h1>Admin Dashboard</h1>
                <p>Manage and review student project submissions.</p>
            </div>

            <span class="admin-label">⚙ Administrator Panel</span>
        </div>

        <!-- Success or error message -->
        <?php if ($message !== ""): ?>
            <div class="alert <?php echo htmlspecialchars($messageType); ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Statistics -->
        <div class="stats-grid">

            <div class="stat-card">
                <h3>Total Projects</h3>
                <div class="number">
                    <?php echo (int)$totalProjects; ?>
                </div>
            </div>

            <div class="stat-card">
                <h3>Pending Review</h3>
                <div class="number">
                    <?php echo (int)$pendingProjects; ?>
                </div>
            </div>

            <div class="stat-card">
                <h3>Approved</h3>
                <div class="number">
                    <?php echo (int)$approvedProjects; ?>
                </div>
            </div>

            <div class="stat-card">
                <h3>Rejected</h3>
                <div class="number">
                    <?php echo (int)$rejectedProjects; ?>
                </div>
            </div>

        </div>

        <!-- Pending projects -->
        <h2 class="section-title">Projects Waiting for Approval</h2>

        <?php if (count($pendingList) > 0): ?>

            <div class="project-list">

                <?php foreach ($pendingList as $project): ?>

                    <div class="project-card">

                        <h3>
                            <?php echo htmlspecialchars($project['title']); ?>
                        </h3>

                        <span class="category">
                            <?php echo htmlspecialchars($project['category']); ?>
                        </span>

                        <br>

                        <span class="status">● Pending Review</span>

                        <p class="description">
                            <?php echo nl2br(htmlspecialchars($project['description'])); ?>
                        </p>

                        <div class="project-info">

                            <strong>Project ID:</strong>
                            <?php echo (int)$project['id']; ?>
                            <br>

                            <strong>Skills Needed:</strong>
                            <?php echo htmlspecialchars($project['skills_needed'] ?: 'Not specified'); ?>
                            <br>

                            <strong>Team Size:</strong>
                            <?php echo (int)$project['team_size']; ?>
                            <br>

                            <strong>Submitted On:</strong>
                            <?php echo date("d M Y", strtotime($project['created_at'])); ?>

                        </div>

                        <!-- Approve and Reject forms -->
                        <div class="action-buttons">

                            <form method="POST"
                                  onsubmit="return confirm('Are you sure you want to approve this project?');">

                                <input type="hidden" name="csrf_token"
                                       value="<?php echo htmlspecialchars($_SESSION['admin_csrf']); ?>">

                                <input type="hidden" name="project_id"
                                       value="<?php echo (int)$project['id']; ?>">

                                <input type="hidden" name="action" value="approve">

                                <button type="submit" class="approve-btn">
                                    ✓ Approve
                                </button>

                            </form>

                            <form method="POST"
                                  onsubmit="return confirm('Are you sure you want to reject this project?');">

                                <input type="hidden" name="csrf_token"
                                       value="<?php echo htmlspecialchars($_SESSION['admin_csrf']); ?>">

                                <input type="hidden" name="project_id"
                                       value="<?php echo (int)$project['id']; ?>">

                                <input type="hidden" name="action" value="reject">

                                <button type="submit" class="reject-btn">
                                    ✕ Reject
                                </button>

                            </form>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php else: ?>

            <div class="empty-message">
                <h3>All caught up!</h3>
                <p>
                    There are currently no pending projects waiting for review.
                    New student submissions will appear here.
                </p>
            </div>

        <?php endif; ?>

        <div class="footer">
            © <?php echo date("Y"); ?> IdeaVault | Admin Management System
        </div>

    </main>

</div>

</body>
</html>
