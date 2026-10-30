
<?php
session_start();

// Database connection
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
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database connection failed. Please check your XAMPP settings.");
}

// Function to safely display data
function clean($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

// Get email entered by the student
$email = trim($_GET['email'] ?? '');

$projects = [];
$error = "";
$searched = false;

if ($email !== "") {
    $searched = true;

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        try {
            // Fetch projects submitted using this email
            $sql = "SELECT id, title, category, description,
                           skills_needed, team_size, status, created_at
                    FROM projects
                    WHERE submitted_by_email = :email
                    ORDER BY created_at DESC";

            $stmt = $conn->prepare($sql);
            $stmt->execute([':email' => $email]);

            $projects = $stmt->fetchAll();

        } catch (PDOException $e) {
            $error = "Unable to load your projects. Please check the database setup.";
        }
    }
}

// Count projects according to their status
$totalProjects = count($projects);
$pendingProjects = 0;
$approvedProjects = 0;
$rejectedProjects = 0;

foreach ($projects as $project) {
    if ($project['status'] === 'pending') {
        $pendingProjects++;
    } elseif ($project['status'] === 'approved') {
        $approvedProjects++;
    } elseif ($project['status'] === 'rejected') {
        $rejectedProjects++;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Projects | IdeaVault</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #07152f, #0b1d3d, #102d58);
            color: white;
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            flex-shrink: 0;
            background: rgba(4, 15, 35, 0.95);
            padding: 30px 20px;
            border-right: 1px solid rgba(0, 200, 255, 0.2);
        }

        .logo {
            text-align: center;
            font-size: 26px;
            font-weight: bold;
            color: #00d9ff;
            margin-bottom: 40px;
        }

        .sidebar a {
            display: block;
            color: #b8cce8;
            text-decoration: none;
            padding: 14px 15px;
            margin-bottom: 10px;
            border-radius: 10px;
            transition: 0.3s;
            font-size: 15px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: linear-gradient(90deg, #0879c9, #00bfff);
            color: white;
            box-shadow: 0 0 15px rgba(0, 191, 255, 0.3);
        }

        /* Main content */
        .main-content {
            flex: 1;
            min-width: 0;
            padding: 35px;
        }

        .top-heading {
            margin-bottom: 28px;
        }

        .top-heading h1 {
            font-size: 30px;
            margin-bottom: 10px;
        }

        .top-heading p {
            color: #a9c5e8;
            line-height: 1.6;
            font-size: 15px;
        }

        /* Search card */
        .search-card {
            max-width: 750px;
            padding: 25px;
            margin-bottom: 30px;
            background: rgba(15, 39, 75, 0.78);
            border: 1px solid rgba(0, 195, 255, 0.25);
            border-radius: 15px;
        }

        .search-card h2 {
            color: #00d9ff;
            font-size: 20px;
            margin-bottom: 10px;
        }

        .search-card p {
            color: #a9c5e8;
            font-size: 14px;
            margin-bottom: 18px;
            line-height: 1.6;
        }

        .search-form {
            display: flex;
            gap: 12px;
        }

        .search-form input {
            flex: 1;
            min-width: 0;
            padding: 13px 15px;
            border: 1px solid #31547c;
            border-radius: 9px;
            background: #0a1c38;
            color: white;
            outline: none;
        }

        .search-form input:focus {
            border-color: #00d9ff;
            box-shadow: 0 0 10px rgba(0, 217, 255, 0.2);
        }

        .search-btn {
            padding: 13px 22px;
            border: none;
            border-radius: 9px;
            background: linear-gradient(90deg, #0879d1, #00c8f5);
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        .search-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 200, 255, 0.25);
        }

        /* Messages */
        .message {
            padding: 14px;
            margin-bottom: 22px;
            border-radius: 9px;
            font-size: 14px;
        }

        .error {
            background: rgba(255, 70, 90, 0.15);
            border: 1px solid #ff5268;
            color: #ffb2bd;
        }

        /* Statistics */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 18px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: rgba(15, 39, 75, 0.78);
            border: 1px solid rgba(0, 195, 255, 0.22);
            padding: 22px;
            border-radius: 14px;
        }

        .stat-card h3 {
            color: #a9c5e8;
            font-size: 13px;
            font-weight: normal;
            margin-bottom: 12px;
        }

        .stat-card p {
            color: #00d9ff;
            font-size: 28px;
            font-weight: bold;
        }

        /* Project cards */
        .section-heading {
            font-size: 22px;
            margin-bottom: 20px;
        }

        .project-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 22px;
        }

        .project-card {
            background: rgba(15, 39, 75, 0.78);
            border: 1px solid rgba(0, 195, 255, 0.25);
            border-radius: 15px;
            padding: 25px;
            transition: 0.3s;
        }

        .project-card:hover {
            transform: translateY(-4px);
            border-color: #00c8f5;
            box-shadow: 0 8px 25px rgba(0, 190, 255, 0.12);
        }

        .project-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 15px;
        }

        .category {
            display: inline-block;
            background: rgba(0, 191, 255, 0.15);
            border: 1px solid rgba(0, 191, 255, 0.4);
            color: #54ddff;
            padding: 7px 12px;
            border-radius: 20px;
            font-size: 12px;
        }

        .status {
            display: inline-block;
            padding: 7px 11px;
            border-radius: 20px;
            font-size: 12px;
            text-transform: capitalize;
            white-space: nowrap;
        }

        .status.pending {
            color: #ffd98a;
            background: rgba(255, 190, 60, 0.13);
            border: 1px solid rgba(255, 190, 60, 0.4);
        }

        .status.approved {
            color: #8dffd2;
            background: rgba(20, 180, 120, 0.13);
            border: 1px solid rgba(32, 201, 151, 0.4);
        }

        .status.rejected {
            color: #ffb2bd;
            background: rgba(255, 70, 90, 0.13);
            border: 1px solid rgba(255, 82, 104, 0.4);
        }

        .project-card h3 {
            font-size: 20px;
            line-height: 1.4;
            margin-bottom: 12px;
            overflow-wrap: anywhere;
        }

        .description {
            color: #b8cce8;
            font-size: 14px;
            line-height: 1.7;
            margin-bottom: 18px;
            overflow-wrap: anywhere;
        }

        .project-info {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            color: #8faed1;
            font-size: 13px;
            margin-bottom: 18px;
        }

        .skills-heading {
            color: #00d9ff;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .skills {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 20px;
        }

        .skill-tag {
            background: rgba(0, 191, 255, 0.13);
            color: #80e6ff;
            border: 1px solid rgba(0, 191, 255, 0.3);
            padding: 7px 11px;
            border-radius: 20px;
            font-size: 12px;
        }

        .view-btn {
            display: inline-block;
            padding: 11px 18px;
            border-radius: 8px;
            background: linear-gradient(90deg, #0879d1, #00c8f5);
            color: white;
            text-decoration: none;
            font-size: 13px;
            font-weight: bold;
            transition: 0.3s;
        }

        .view-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 200, 255, 0.25);
        }

        .not-available {
            color: #8faed1;
            font-size: 13px;
        }

        /* Empty state */
        .empty-state {
            background: rgba(15, 39, 75, 0.75);
            border: 1px solid rgba(0, 195, 255, 0.2);
            border-radius: 15px;
            padding: 40px 20px;
            text-align: center;
        }

        .empty-state h2 {
            color: #00d9ff;
            margin-bottom: 12px;
        }

        .empty-state p {
            color: #a9c5e8;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .submit-link {
            color: #54ddff;
            text-decoration: none;
            font-weight: bold;
        }

        .footer {
            text-align: center;
            color: #7895b8;
            font-size: 13px;
            margin-top: 35px;
        }

        /* Responsive design */
        @media (max-width: 1000px) {
            .stats-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .project-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                padding: 15px;
                border-right: none;
                border-bottom: 1px solid rgba(0, 200, 255, 0.2);
            }

            .logo {
                margin-bottom: 15px;
            }

            .sidebar a {
                display: inline-block;
                margin: 3px;
                padding: 10px;
                font-size: 13px;
            }

            .main-content {
                padding: 20px 14px;
            }

            .search-form {
                flex-direction: column;
            }

            .search-btn {
                width: 100%;
            }
        }

        @media (max-width: 450px) {
            .stats-grid {
                grid-template-columns: 1fr 1fr;
            }

            .project-top {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <!-- Sidebar navigation -->
    <aside class="sidebar">

        <div class="logo">IdeaVault</div>

        <nav>
            <a href="dashboard.html">Dashboard</a>
            <a href="explore-ideas.php">Explore Ideas</a>
            <a href="submit-idea.php">Submit Idea</a>
            <a href="find-team.php">Find Team</a>
            <a href="my-projects.php" class="active">My Projects</a>
            <a href="project-dashboard.php">Project Dashboard</a>
            <a href="admin-dashboard.php">Admin Dashboard</a>
        </nav>

    </aside>

    <!-- Main content -->
    <main class="main-content">

        <div class="top-heading">
            <h1>My Projects 📁</h1>
            <p>
                View the ideas you have submitted and track their approval status.
            </p>
        </div>

        <!-- Email search -->
        <div class="search-card">

            <h2>Find Your Submitted Projects</h2>

            <p>
                Enter the same email address you used while submitting your
                project idea.
            </p>

            <form method="GET" action="my-projects.php" class="search-form"
                  id="emailForm">

                <input
                    type="email"
                    name="email"
                    id="email"
                    placeholder="Enter your registered email address"
                    value="<?php echo clean($email); ?>"
                    maxlength="254"
                    required
                >

                <button type="submit" class="search-btn">
                    View My Projects
                </button>

            </form>

        </div>

        <?php if ($error !== ""): ?>

            <div class="message error">
                <?php echo clean($error); ?>
            </div>

        <?php endif; ?>

        <?php if ($searched && $error === ""): ?>

            <!-- Project statistics -->
            <div class="stats-grid">

                <div class="stat-card">
                    <h3>Total Projects</h3>
                    <p><?php echo $totalProjects; ?></p>
                </div>

                <div class="stat-card">
                    <h3>Approved</h3>
                    <p><?php echo $approvedProjects; ?></p>
                </div>

                <div class="stat-card">
                    <h3>Pending</h3>
                    <p><?php echo $pendingProjects; ?></p>
                </div>

                <div class="stat-card">
                    <h3>Rejected</h3>
                    <p><?php echo $rejectedProjects; ?></p>
                </div>

            </div>

            <h2 class="section-heading">Your Submitted Ideas</h2>

            <?php if (count($projects) > 0): ?>

                <div class="project-grid">

                    <?php foreach ($projects as $project): ?>

                        <?php
                        $status = strtolower($project['status'] ?? 'pending');

                        // Allow only known status styles
                        if (!in_array($status, ['pending', 'approved', 'rejected'], true)) {
                            $status = 'pending';
                        }

                        $description = $project['description'] ?? '';

                        if (strlen($description) > 180) {
                            $description = substr($description, 0, 180) . "...";
                        }

                        $projectDate = !empty($project['created_at'])
                            ? date("d M Y", strtotime($project['created_at']))
                            : "Not available";
                        ?>

                        <div class="project-card">

                            <div class="project-top">

                                <span class="category">
                                    <?php echo clean($project['category']); ?>
                                </span>

                                <span class="status <?php echo clean($status); ?>">
                                    <?php echo clean($status); ?>
                                </span>

                            </div>

                            <h3>
                                <?php echo clean($project['title']); ?>
                            </h3>

                            <p class="description">
                                <?php echo clean($description); ?>
                            </p>

                            <div class="project-info">

                                <span>
                                    📅 <?php echo clean($projectDate); ?>
                                </span>

                                <span>
                                    👥 <?php echo (int)$project['team_size']; ?> Members
                                </span>

                                <span>
                                    ID: #<?php echo (int)$project['id']; ?>
                                </span>

                            </div>

                            <h4 class="skills-heading">Skills Required</h4>

                            <div class="skills">

                                <?php
                                $skillList = explode(
                                    ',',
                                    $project['skills_needed'] ?? ''
                                );

                                foreach ($skillList as $skill) {
                                    $skill = trim($skill);

                                    if ($skill !== "") {
                                        echo '<span class="skill-tag">'
                                            . clean($skill)
                                            . '</span>';
                                    }
                                }
                                ?>

                            </div>

                            <?php if ($status === 'approved'): ?>

                                <a
                                    href="project-details.php?id=<?php echo (int)$project['id']; ?>"
                                    class="view-btn"
                                >
                                    View Project Details →
                                </a>

                            <?php else: ?>

                                <span class="not-available">
                                    Your project is waiting for approval or review.
                                </span>

                            <?php endif; ?>

                        </div>

                    <?php endforeach; ?>

                </div>

            <?php else: ?>

                <div class="empty-state">

                    <h2>No Projects Found</h2>

                    <p>
                        We could not find any projects submitted using this
                        email address. Check your email or submit a new idea.
                    </p>

                    <a href="submit-idea.php" class="submit-link">
                        Submit a New Idea →
                    </a>

                </div>

            <?php endif; ?>

        <?php else: ?>

            <div class="empty-state">

                <h2>Manage Your Ideas 💡</h2>

                <p>
                    Your submitted projects will appear here. Enter your
                    registered email address above to view their details
                    and approval status.
                </p>

                <a href="submit-idea.php" class="submit-link">
                    Submit Your First Idea →
                </a>

            </div>

        <?php endif; ?>

        <div class="footer">
            © 2026 IdeaVault | Connect • Collaborate • Create
        </div>

    </main>

</div>

<!-- JavaScript form validation -->
<script>
    document.getElementById("emailForm").addEventListener("submit", function(event) {

        const email = document.getElementById("email").value.trim();

        if (email === "") {
            alert("Please enter your email address.");
            event.preventDefault();
            return;
        }

        if (!email.includes("@") || !email.includes(".")) {
            alert("Please enter a valid email address.");
            event.preventDefault();
        }

    });
</script>

</body>
</html>
