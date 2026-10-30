
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

// Get project ID from URL
$project_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

$project = null;
$error = "";

if (!$project_id || $project_id < 1) {
    $error = "Invalid project ID. Please select a valid project.";
} else {
    try {
        // Fetch approved project details
        $sql = "SELECT id, title, category, description,
                       skills_needed, team_size, status, created_at
                FROM projects
                WHERE id = :id AND status = 'approved'
                LIMIT 1";

        $stmt = $conn->prepare($sql);
        $stmt->execute([':id' => $project_id]);

        $project = $stmt->fetch();

        if (!$project) {
            $error = "Project not found or it has not been approved yet.";
        }

    } catch (PDOException $e) {
        $error = "Unable to load project details. Please try again later.";
    }
}

// Format project date
$project_date = "";

if ($project && !empty($project['created_at'])) {
    $project_date = date(
        "d M Y",
        strtotime($project['created_at'])
    );
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?php echo $project ? clean($project['title']) : "Project Details"; ?>
        | IdeaVault
    </title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(
                135deg,
                #07152f,
                #0b1d3d,
                #102d58
            );
            color: white;
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            background: rgba(4, 15, 35, 0.95);
            padding: 30px 20px;
            border-right: 1px solid rgba(0, 200, 255, 0.2);
            flex-shrink: 0;
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
            padding: 35px;
            min-width: 0;
        }

        .top-heading {
            margin-bottom: 30px;
        }

        .top-heading h1 {
            font-size: 30px;
            margin-bottom: 10px;
        }

        .top-heading p {
            color: #a9c5e8;
            font-size: 15px;
        }

        /* Back button */
        .back-link {
            display: inline-block;
            margin-bottom: 25px;
            color: #00d9ff;
            text-decoration: none;
            font-size: 14px;
        }

        .back-link:hover {
            color: white;
        }

        /* Project card */
        .project-card {
            max-width: 950px;
            margin: auto;
            padding: 35px;
            background: rgba(15, 39, 75, 0.78);
            border: 1px solid rgba(0, 195, 255, 0.25);
            border-radius: 18px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.25);
            backdrop-filter: blur(12px);
        }

        .project-header {
            border-bottom: 1px solid rgba(150, 190, 230, 0.2);
            padding-bottom: 25px;
            margin-bottom: 25px;
        }

        .category {
            display: inline-block;
            background: rgba(0, 191, 255, 0.15);
            border: 1px solid rgba(0, 191, 255, 0.4);
            color: #54ddff;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 13px;
            margin-bottom: 18px;
        }

        .project-header h2 {
            font-size: 30px;
            margin-bottom: 15px;
            line-height: 1.4;
            overflow-wrap: anywhere;
        }

        .project-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            color: #a9c5e8;
            font-size: 14px;
        }

        .section {
            margin-bottom: 30px;
        }

        .section h3 {
            color: #00d9ff;
            font-size: 19px;
            margin-bottom: 15px;
        }

        .section p {
            color: #d0e0f5;
            font-size: 15px;
            line-height: 1.8;
            white-space: pre-line;
            overflow-wrap: anywhere;
        }

        /* Information boxes */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
            margin-bottom: 30px;
        }

        .info-box {
            background: rgba(5, 24, 52, 0.65);
            border: 1px solid rgba(0, 195, 255, 0.2);
            padding: 20px;
            border-radius: 12px;
        }

        .info-box h4 {
            color: #8eafd5;
            font-size: 13px;
            margin-bottom: 10px;
            font-weight: normal;
        }

        .info-box p {
            color: #00d9ff;
            font-size: 18px;
            font-weight: bold;
            overflow-wrap: anywhere;
        }

        /* Skills */
        .skills {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .skill-tag {
            background: rgba(0, 191, 255, 0.13);
            color: #80e6ff;
            border: 1px solid rgba(0, 191, 255, 0.3);
            padding: 9px 14px;
            border-radius: 20px;
            font-size: 13px;
        }

        /* Action buttons */
        .button-group {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px solid rgba(150, 190, 230, 0.2);
        }

        .btn {
            display: inline-block;
            padding: 13px 25px;
            border-radius: 9px;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
            border: none;
        }

        .primary-btn {
            background: linear-gradient(90deg, #0879d1, #00c8f5);
            color: white;
        }

        .primary-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 20px rgba(0, 200, 255, 0.35);
        }

        .secondary-btn {
            background: transparent;
            border: 1px solid #52749b;
            color: #c7dcf5;
        }

        .secondary-btn:hover {
            background: rgba(255, 255, 255, 0.08);
        }

        /* Error message */
        .error-card {
            max-width: 700px;
            margin: 50px auto;
            padding: 35px;
            text-align: center;
            background: rgba(15, 39, 75, 0.78);
            border: 1px solid rgba(255, 82, 104, 0.4);
            border-radius: 18px;
        }

        .error-card h2 {
            color: #ff8496;
            margin-bottom: 15px;
        }

        .error-card p {
            color: #c5d8ef;
            line-height: 1.6;
            margin-bottom: 25px;
        }

        /* Footer */
        .footer {
            text-align: center;
            color: #7895b8;
            font-size: 13px;
            margin-top: 35px;
        }

        /* Responsive design */
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

            .project-card {
                padding: 22px;
            }

            .project-header h2 {
                font-size: 24px;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .button-group {
                flex-direction: column;
            }

            .btn {
                text-align: center;
                width: 100%;
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
            <a href="explore-ideas.php" class="active">Explore Ideas</a>
            <a href="submit-idea.php">Submit Idea</a>
            <a href="find-team.php">Find Team</a>
            <a href="my-projects.php">My Projects</a>
            <a href="project-dashboard.php">Project Dashboard</a>
            <a href="admin-dashboard.php">Admin Dashboard</a>
        </nav>

    </aside>

    <!-- Main page -->
    <main class="main-content">

        <div class="top-heading">
            <h1>Project Details</h1>
            <p>Explore project information and discover collaboration opportunities.</p>
        </div>

        <a href="explore-ideas.php" class="back-link">
            &larr; Back to Explore Ideas
        </a>

        <?php if ($project): ?>

            <div class="project-card">

                <!-- Project heading -->
                <div class="project-header">

                    <span class="category">
                        <?php echo clean($project['category']); ?>
                    </span>

                    <h2>
                        <?php echo clean($project['title']); ?>
                    </h2>

                    <div class="project-meta">
                        <span>
                            Project ID: #<?php echo (int)$project['id']; ?>
                        </span>

                        <span>
                            Posted on: <?php echo clean($project_date); ?>
                        </span>

                        <span>
                            Status: Approved
                        </span>
                    </div>

                </div>

                <!-- Project description -->
                <div class="section">

                    <h3>About This Project</h3>

                    <p><?php echo clean($project['description']); ?></p>

                </div>

                <!-- Project information -->
                <div class="section">

                    <h3>Project Information</h3>

                    <div class="info-grid">

                        <div class="info-box">
                            <h4>Project Category</h4>
                            <p><?php echo clean($project['category']); ?></p>
                        </div>

                        <div class="info-box">
                            <h4>Required Team Size</h4>
                            <p>
                                <?php echo (int)$project['team_size']; ?>
                                Members
                            </p>
                        </div>

                    </div>

                </div>

                <!-- Required skills -->
                <div class="section">

                    <h3>Skills Required</h3>

                    <div class="skills">

                        <?php
                        $skill_list = explode(
                            ',',
                            $project['skills_needed'] ?? ''
                        );

                        foreach ($skill_list as $skill) {
                            $skill = trim($skill);

                            if ($skill !== "") {
                                echo '<span class="skill-tag">'
                                    . clean($skill)
                                    . '</span>';
                            }
                        }
                        ?>

                    </div>

                </div>

                <!-- Action buttons -->
                <div class="button-group">

                    <a href="find-team.php" class="btn primary-btn">
                        Find Team Members
                    </a>

                    <button
                        type="button"
                        class="btn secondary-btn"
                        id="shareButton"
                    >
                        Share Project
                    </button>

                    <a href="explore-ideas.php" class="btn secondary-btn">
                        Back to Explore
                    </a>

                </div>

            </div>

        <?php else: ?>

            <!-- Display error if project is unavailable -->
            <div class="error-card">

                <h2>Project Not Found</h2>

                <p><?php echo clean($error); ?></p>

                <a href="explore-ideas.php" class="btn primary-btn">
                    Explore Other Projects
                </a>

            </div>

        <?php endif; ?>

        <div class="footer">
            © 2026 IdeaVault | Connect • Collaborate • Create
        </div>

    </main>

</div>

<!-- JavaScript for sharing the project -->
<script>
    const shareButton = document.getElementById("shareButton");

    if (shareButton) {
        shareButton.addEventListener("click", async function() {

            const projectTitle = <?php
                echo json_encode(
                    $project ? $project['title'] : '',
                    JSON_HEX_TAG | JSON_HEX_AMP |
                    JSON_HEX_APOS | JSON_HEX_QUOT
                );
            ?>;

            const projectURL = window.location.href;

            try {
                if (navigator.share) {
                    await navigator.share({
                        title: projectTitle,
                        text: "Check out this project on IdeaVault!",
                        url: projectURL
                    });
                } else if (navigator.clipboard) {
                    await navigator.clipboard.writeText(projectURL);
                    alert("Project link copied successfully!");
                } else {
                    alert("Please copy this page URL from your browser.");
                }

            } catch (error) {
                if (error.name !== "AbortError") {
                    alert("Unable to share the project. Please copy the URL manually.");
                }
            }

        });
    }
</script>

</body>
</html>
