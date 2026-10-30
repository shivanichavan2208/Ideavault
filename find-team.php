
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

// Function to safely display information
function clean($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$success = "";
$error = "";

// Read optional project ID from URL
$selected_project = filter_input(INPUT_GET, 'project_id', FILTER_VALIDATE_INT);

if (!$selected_project || $selected_project < 1) {
    $selected_project = null;
}

// Handle join request form
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Verify CSRF token
    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        $error = "Invalid form submission. Please refresh the page.";

    } else {

        $project_id = filter_input(
            INPUT_POST,
            'project_id',
            FILTER_VALIDATE_INT
        );

        $student_name = trim($_POST['student_name'] ?? "");
        $email = trim($_POST['email'] ?? "");
        $student_skills = trim($_POST['student_skills'] ?? "");
        $message = trim($_POST['message'] ?? "");

        // Validate form fields
        if (
            !$project_id ||
            $student_name === "" ||
            $email === "" ||
            $student_skills === ""
        ) {
            $error = "Please fill in all required fields.";

        } elseif (strlen($student_name) > 100) {
            $error = "Name must not exceed 100 characters.";

        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Please enter a valid email address.";

        } elseif (strlen($student_skills) > 255) {
            $error = "Skills must not exceed 255 characters.";

        } elseif (strlen($message) > 1000) {
            $error = "Message must not exceed 1000 characters.";

        } else {

            try {
                // Check whether project exists and is approved
                $check = $conn->prepare(
                    "SELECT id FROM projects
                     WHERE id = :id AND status = 'approved'"
                );

                $check->execute([':id' => $project_id]);

                if (!$check->fetch()) {
                    $error = "This project is unavailable.";

                } else {

                    // Insert join request
                    $sql = "INSERT INTO team_requests
                            (project_id, student_name, email,
                             student_skills, message, status)
                            VALUES
                            (:project_id, :student_name, :email,
                             :student_skills, :message, 'pending')";

                    $stmt = $conn->prepare($sql);

                    $stmt->execute([
                        ':project_id' => $project_id,
                        ':student_name' => $student_name,
                        ':email' => $email,
                        ':student_skills' => $student_skills,
                        ':message' => $message
                    ]);

                    $success = "Your team request has been submitted successfully!";

                    // Clear the form
                    $_POST = [];

                    // Generate a new token
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                }

            } catch (PDOException $e) {
                $error = "Unable to submit your request. Please try again.";
            }
        }

        $selected_project = $project_id ?: $selected_project;
    }
}

// Fetch approved projects
try {
    if ($selected_project) {

        $sql = "SELECT * FROM projects
                WHERE status = 'approved'
                AND id = :project_id
                ORDER BY created_at DESC";

        $stmt = $conn->prepare($sql);
        $stmt->execute([':project_id' => $selected_project]);

    } else {

        $sql = "SELECT * FROM projects
                WHERE status = 'approved'
                ORDER BY created_at DESC";

        $stmt = $conn->query($sql);
    }

    $projects = $stmt->fetchAll();

} catch (PDOException $e) {
    $projects = [];
    $error = "Unable to load projects. Please try again later.";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Find Team | IdeaVault</title>

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
            min-width: 0;
            padding: 35px;
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
            line-height: 1.6;
        }

        /* Search section */
        .search-box {
            max-width: 650px;
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
        }

        .search-box input {
            flex: 1;
            min-width: 0;
            padding: 13px 16px;
            border-radius: 9px;
            border: 1px solid #31547c;
            background: #0a1c38;
            color: white;
            outline: none;
        }

        .search-box input:focus {
            border-color: #00d9ff;
            box-shadow: 0 0 10px rgba(0, 217, 255, 0.2);
        }

        .search-box button {
            padding: 13px 22px;
            border: none;
            border-radius: 9px;
            background: linear-gradient(90deg, #0879d1, #00c8f5);
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        /* Messages */
        .message {
            padding: 14px;
            margin-bottom: 22px;
            border-radius: 9px;
            line-height: 1.5;
            font-size: 14px;
        }

        .success {
            background: rgba(20, 180, 120, 0.15);
            border: 1px solid #20c997;
            color: #8dffd2;
        }

        .error {
            background: rgba(255, 70, 90, 0.15);
            border: 1px solid #ff5268;
            color: #ffb2bd;
        }

        /* Project grid */
        .project-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 25px;
        }

        .project-card {
            background: rgba(15, 39, 75, 0.78);
            border: 1px solid rgba(0, 195, 255, 0.25);
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            transition: 0.3s;
        }

        .project-card:hover {
            transform: translateY(-5px);
            border-color: #00c8f5;
            box-shadow: 0 10px 25px rgba(0, 190, 255, 0.12);
        }

        .category {
            display: inline-block;
            background: rgba(0, 191, 255, 0.15);
            border: 1px solid rgba(0, 191, 255, 0.4);
            color: #54ddff;
            padding: 7px 13px;
            border-radius: 20px;
            font-size: 12px;
            margin-bottom: 15px;
        }

        .project-card h2 {
            font-size: 21px;
            margin-bottom: 12px;
            line-height: 1.4;
            overflow-wrap: anywhere;
        }

        .project-description {
            color: #b8cce8;
            font-size: 14px;
            line-height: 1.7;
            margin-bottom: 20px;
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
            margin-bottom: 22px;
        }

        .skill-tag {
            background: rgba(0, 191, 255, 0.13);
            color: #80e6ff;
            border: 1px solid rgba(0, 191, 255, 0.3);
            padding: 7px 11px;
            border-radius: 20px;
            font-size: 12px;
        }

        /* Join form */
        .join-form {
            background: rgba(5, 24, 52, 0.65);
            border: 1px solid rgba(0, 195, 255, 0.2);
            padding: 20px;
            border-radius: 12px;
            margin-top: 15px;
        }

        .join-form h3 {
            color: #00d9ff;
            font-size: 17px;
            margin-bottom: 18px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            color: #d0e0f5;
            font-size: 13px;
            margin-bottom: 8px;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #31547c;
            background: #0a1c38;
            color: white;
            outline: none;
            font-size: 13px;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            border-color: #00d9ff;
            box-shadow: 0 0 8px rgba(0, 217, 255, 0.2);
        }

        .form-group input::placeholder,
        .form-group textarea::placeholder {
            color: #7895b8;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 75px;
        }

        .join-btn {
            width: 100%;
            padding: 13px;
            border: none;
            border-radius: 9px;
            background: linear-gradient(90deg, #0879d1, #00c8f5);
            color: white;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        .join-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 18px rgba(0, 200, 255, 0.3);
        }

        /* Empty state */
        .empty-state {
            background: rgba(15, 39, 75, 0.75);
            border: 1px solid rgba(0, 195, 255, 0.2);
            border-radius: 15px;
            padding: 45px 20px;
            text-align: center;
        }

        .empty-state h2 {
            color: #00d9ff;
            margin-bottom: 12px;
        }

        .empty-state p {
            color: #a9c5e8;
            line-height: 1.6;
        }

        /* Footer */
        .footer {
            text-align: center;
            color: #7895b8;
            font-size: 13px;
            margin-top: 35px;
        }

        /* Responsive layout */
        @media (max-width: 950px) {
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

            .search-box {
                flex-direction: column;
            }

            .search-box button {
                width: 100%;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <!-- Sidebar -->
    <aside class="sidebar">

        <div class="logo">IdeaVault</div>

        <nav>
            <a href="dashboard.html">Dashboard</a>
            <a href="explore-ideas.php">Explore Ideas</a>
            <a href="submit-idea.php">Submit Idea</a>
            <a href="find-team.php" class="active">Find Team</a>
            <a href="my-projects.php">My Projects</a>
            <a href="project-dashboard.php">Project Dashboard</a>
            <a href="admin-dashboard.php">Admin Dashboard</a>
        </nav>

    </aside>

    <!-- Main content -->
    <main class="main-content">

        <div class="top-heading">
            <h1>Find Your Team 🚀</h1>
            <p>
                Discover exciting student projects, share your skills
                and collaborate with other students.
            </p>
        </div>

        <!-- Search projects -->
        <div class="search-box">

            <input
                type="text"
                id="searchInput"
                placeholder="Search projects by title or category..."
            >

            <button type="button" id="searchButton">
                Search
            </button>

        </div>

        <!-- Success message -->
        <?php if ($success !== ""): ?>
            <div class="message success">
                <?php echo clean($success); ?>
            </div>
        <?php endif; ?>

        <!-- Error message -->
        <?php if ($error !== ""): ?>
            <div class="message error">
                <?php echo clean($error); ?>
            </div>
        <?php endif; ?>

        <!-- Project listing -->
        <?php if (count($projects) > 0): ?>

            <div class="project-grid" id="projectGrid">

                <?php foreach ($projects as $project): ?>

                    <div class="project-card">

                        <span class="category">
                            <?php echo clean($project['category']); ?>
                        </span>

                        <h2 class="project-title">
                            <?php echo clean($project['title']); ?>
                        </h2>

                        <p class="project-description">
                            <?php
                            $description = $project['description'];

                            if (strlen($description) > 160) {
                                $description = substr($description, 0, 160) . "...";
                            }

                            echo clean($description);
                            ?>
                        </p>

                        <div class="project-info">
                            <span>
                                👥 Team Size:
                                <?php echo (int)$project['team_size']; ?>
                            </span>

                            <span>
                                📁 Project ID:
                                #<?php echo (int)$project['id']; ?>
                            </span>
                        </div>

                        <h3 class="skills-heading">Skills Required</h3>

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

                        <a
                            href="project-details.php?id=<?php echo (int)$project['id']; ?>"
                            style="display:inline-block; color:#54ddff; margin-bottom:15px; text-decoration:none; font-size:13px;"
                        >
                            View Project Details →
                        </a>

                        <!-- Join project form -->
                        <form
                            method="POST"
                            action="find-team.php<?php
                                echo '?project_id=' . (int)$project['id'];
                            ?>"
                            class="join-form joinForm"
                        >

                            <h3>Request to Join This Project</h3>

                            <input
                                type="hidden"
                                name="csrf_token"
                                value="<?php echo clean($_SESSION['csrf_token']); ?>"
                            >

                            <input
                                type="hidden"
                                name="project_id"
                                value="<?php echo (int)$project['id']; ?>"
                            >

                            <div class="form-group">
                                <label>Your Name *</label>

                                <input
                                    type="text"
                                    name="student_name"
                                    placeholder="Enter your full name"
                                    maxlength="100"
                                    required
                                >
                            </div>

                            <div class="form-group">
                                <label>Email Address *</label>

                                <input
                                    type="email"
                                    name="email"
                                    placeholder="Enter your email"
                                    maxlength="254"
                                    required
                                >
                            </div>

                            <div class="form-group">
                                <label>Your Skills *</label>

                                <input
                                    type="text"
                                    name="student_skills"
                                    placeholder="Example: HTML, CSS, PHP"
                                    maxlength="255"
                                    required
                                >
                            </div>

                            <div class="form-group">
                                <label>Why do you want to join?</label>

                                <textarea
                                    name="message"
                                    placeholder="Write a short message..."
                                    maxlength="1000"
                                ></textarea>
                            </div>

                            <button type="submit" class="join-btn">
                                Send Join Request
                            </button>

                        </form>

                    </div>

                <?php endforeach; ?>

            </div>

            <div class="empty-state" id="noResults" style="display:none; margin-top:20px;">
                <h2>No Matching Projects</h2>
                <p>Try searching with a different project name or category.</p>
            </div>

        <?php else: ?>

            <div class="empty-state">
                <h2>No Projects Available</h2>

                <p>
                    There are currently no approved projects.
                    Please check again later or submit your own project idea.
                </p>

                <br>

                <a
                    href="submit-idea.php"
                    style="color:#54ddff; text-decoration:none;"
                >
                    Submit Your Idea →
                </a>
            </div>

        <?php endif; ?>

        <div class="footer">
            © 2026 IdeaVault | Connect • Collaborate • Create
        </div>

    </main>

</div>

<!-- JavaScript search and validation -->
<script>

    // Search projects
    const searchInput = document.getElementById("searchInput");
    const searchButton = document.getElementById("searchButton");

    function searchProjects() {

        const searchValue = searchInput.value.toLowerCase().trim();

        const projectCards = document.querySelectorAll(".project-card");

        let visibleCount = 0;

        projectCards.forEach(function(card) {

            const title = card.querySelector(".project-title")
                .textContent.toLowerCase();

            const category = card.querySelector(".category")
                .textContent.toLowerCase();

            const skills = card.querySelector(".skills")
                .textContent.toLowerCase();

            if (
                title.includes(searchValue) ||
                category.includes(searchValue) ||
                skills.includes(searchValue)
            ) {
                card.style.display = "block";
                visibleCount++;
            } else {
                card.style.display = "none";
            }
        });

        const noResults = document.getElementById("noResults");

        if (noResults) {
            noResults.style.display =
                visibleCount === 0 ? "block" : "none";
        }
    }

    searchButton.addEventListener("click", searchProjects);

    searchInput.addEventListener("input", searchProjects);

    // Validate join request forms
    document.querySelectorAll(".joinForm").forEach(function(form) {

        form.addEventListener("submit", function(event) {

            const name = form.querySelector(
                '[name="student_name"]'
            ).value.trim();

            const email = form.querySelector(
                '[name="email"]'
            ).value.trim();

            const skills = form.querySelector(
                '[name="student_skills"]'
            ).value.trim();

            if (name === "" || email === "" || skills === "") {
                alert("Please fill in all required fields.");
                event.preventDefault();
                return;
            }

            if (!confirm("Are you sure you want to send this join request?")) {
                event.preventDefault();
            }
        });

    });

</script>

</body>
</html>
