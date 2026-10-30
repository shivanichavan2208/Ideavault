<?php
// ==========================================
// IdeaVault - Project Dashboard
// File: project-dashboard.php
// ==========================================

$host = "localhost";
$dbname = "ideavault";
$username = "root";
$password = "";

try {
    $conn = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get project statistics
    $totalProjects = $conn->query(
        "SELECT COUNT(*) FROM projects"
    )->fetchColumn();

    $approvedProjects = $conn->query(
        "SELECT COUNT(*) FROM projects WHERE status = 'approved'"
    )->fetchColumn();

    $pendingProjects = $conn->query(
        "SELECT COUNT(*) FROM projects WHERE status = 'pending'"
    )->fetchColumn();

    $rejectedProjects = $conn->query(
        "SELECT COUNT(*) FROM projects WHERE status = 'rejected'"
    )->fetchColumn();

    // Get recently approved projects
    $stmt = $conn->prepare(
        "SELECT id, title, category, description,
                skills_needed, team_size, created_at
         FROM projects
         WHERE status = 'approved'
         ORDER BY created_at DESC
         LIMIT 6"
    );

    $stmt->execute();
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database connection failed. Please check your database settings.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Project Dashboard | IdeaVault</title>

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
            color: #ffffff;
        }

        .layout {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 245px;
            background: rgba(5, 18, 38, 0.96);
            padding: 28px 18px;
            border-right: 1px solid rgba(0, 220, 255, 0.15);
        }

        .logo {
            color: #22d3ee;
            font-size: 25px;
            font-weight: bold;
            margin-bottom: 35px;
            text-align: center;
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

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 35px;
            min-width: 0;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .topbar h1 {
            font-size: 30px;
            color: #ffffff;
        }

        .topbar p {
            color: #a9c6e5;
            margin-top: 8px;
        }

        .welcome {
            color: #22d3ee;
            font-weight: bold;
        }

        .primary-btn {
            display: inline-block;
            background: linear-gradient(90deg, #0891b2, #06b6d4);
            color: white;
            text-decoration: none;
            padding: 12px 20px;
            border-radius: 9px;
            font-weight: bold;
            transition: 0.3s;
        }

        .primary-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 18px rgba(6, 182, 212, 0.3);
        }

        /* Statistics */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(150px, 1fr));
            gap: 20px;
            margin-bottom: 35px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(34, 211, 238, 0.2);
            padding: 24px;
            border-radius: 16px;
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

        /* Section */
        .section-title {
            font-size: 23px;
            margin-bottom: 20px;
        }

        .project-grid {
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
            transform: translateY(-4px);
            border-color: #22d3ee;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .project-card h3 {
            color: #ffffff;
            font-size: 20px;
            margin-bottom: 10px;
        }

        .category {
            display: inline-block;
            background: rgba(6, 182, 212, 0.15);
            color: #67e8f9;
            padding: 6px 11px;
            border-radius: 20px;
            font-size: 12px;
            margin-bottom: 12px;
        }

        .description {
            color: #c4d9ef;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .project-info {
            color: #9db9d8;
            font-size: 13px;
            line-height: 1.8;
            margin-bottom: 16px;
        }

        .view-btn {
            display: inline-block;
            padding: 10px 16px;
            border-radius: 8px;
            color: #ffffff;
            background: #087da8;
            text-decoration: none;
            font-size: 14px;
        }

        .view-btn:hover {
            background: #06b6d4;
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

            .project-grid {
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

    <!-- Sidebar Navigation -->
    <aside class="sidebar">

        <div class="logo">IdeaVault</div>

        <a href="dashboard.html">🏠 Dashboard</a>
        <a href="explore-ideas.php">💡 Explore Ideas</a>
        <a href="submit-idea.php">➕ Submit Idea</a>
        <a href="find-team.php">👥 Find Team</a>
        <a href="my-projects.php">📁 My Projects</a>
        <a href="project-dashboard.php" class="active">📊 Project Dashboard</a>
        <a href="admin-dashboard.php">⚙️ Admin Dashboard</a>

    </aside>

    <!-- Main Content -->
    <main class="main-content">

        <div class="topbar">
            <div>
                <h1>Project Dashboard</h1>
                <p>Welcome to your IdeaVault project overview.</p>
            </div>

            <a href="submit-idea.php" class="primary-btn">
                + Submit New Idea
            </a>
        </div>

        <p class="welcome" style="margin-bottom: 22px;">
            Track projects and explore approved student ideas.
        </p>

        <!-- Statistics Cards -->
        <div class="stats-grid">

            <div class="stat-card">
                <h3>Total Projects</h3>
                <div class="number">
                    <?php echo (int)$totalProjects; ?>
                </div>
            </div>

            <div class="stat-card">
                <h3>Approved Projects</h3>
                <div class="number">
                    <?php echo (int)$approvedProjects; ?>
                </div>
            </div>

            <div class="stat-card">
                <h3>Pending Projects</h3>
                <div class="number">
                    <?php echo (int)$pendingProjects; ?>
                </div>
            </div>

            <div class="stat-card">
                <h3>Rejected Projects</h3>
                <div class="number">
                    <?php echo (int)$rejectedProjects; ?>
                </div>
            </div>

        </div>

        <!-- Recent Projects -->
        <h2 class="section-title">Recently Approved Projects</h2>

        <?php if (count($projects) > 0): ?>

            <div class="project-grid">

                <?php foreach ($projects as $project): ?>

                    <div class="project-card">

                        <h3>
                            <?php echo htmlspecialchars($project['title']); ?>
                        </h3>

                        <span class="category">
                            <?php echo htmlspecialchars($project['category']); ?>
                        </span>

                        <p class="description">
                            <?php
                            $description = $project['description'];

                            if (strlen($description) > 160) {
                                $description = substr($description, 0, 160) . "...";
                            }

                            echo htmlspecialchars($description);
                            ?>
                        </p>

                        <div class="project-info">
                            <strong>Skills:</strong>
                            <?php echo htmlspecialchars($project['skills_needed'] ?: 'Not specified'); ?>
                            <br>

                            <strong>Team Size:</strong>
                            <?php echo (int)$project['team_size']; ?>
                            <br>

                            <strong>Created:</strong>
                            <?php echo date("d M Y", strtotime($project['created_at'])); ?>
                        </div>

                        <a
                            href="project-details.php?id=<?php echo (int)$project['id']; ?>"
                            class="view-btn"
                        >
                            View Project Details →
                        </a>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php else: ?>

            <div class="empty-message">
                <h3>No approved projects yet.</h3>
                <p>
                    Approved student projects will appear here.
                    You can submit a new idea using the button above.
                </p>
            </div>

        <?php endif; ?>

        <div class="footer">
            © <?php echo date("Y"); ?> IdeaVault | Project Collaboration Platform
        </div>

    </main>

</div>

</body>
</html>