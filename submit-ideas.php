
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

} catch (PDOException $e) {
    die("Database connection failed. Please check your database settings.");
}

// Variables for messages
$success = "";
$error = "";

// Form values
$title = "";
$category = "";
$description = "";
$skills = "";
$team_size = "";

// CSRF protection token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Check security token
    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        $error = "Invalid form submission. Please refresh the page.";
    } else {

        // Get and clean form data
        $title = trim($_POST['title'] ?? "");
        $category = trim($_POST['category'] ?? "");
        $description = trim($_POST['description'] ?? "");
        $skills = trim($_POST['skills'] ?? "");
        $team_size = trim($_POST['team_size'] ?? "");

        // Validate input
        $allowed_categories = [
            "Web Development",
            "App Development",
            "Artificial Intelligence",
            "Machine Learning",
            "IoT",
            "Cyber Security",
            "Other"
        ];

        if (
            $title == "" ||
            $category == "" ||
            $description == "" ||
            $skills == "" ||
            $team_size == ""
        ) {
            $error = "Please fill in all the fields.";
        } elseif (!in_array($category, $allowed_categories, true)) {
            $error = "Please select a valid project category.";
        } elseif (strlen($title) > 150) {
            $error = "Project title must be less than 150 characters.";
        } elseif (strlen($skills) > 255) {
            $error = "Skills must be less than 255 characters.";
        } elseif (strlen($description) < 20) {
            $error = "Description should contain at least 20 characters.";
        } elseif (
            !filter_var($team_size, FILTER_VALIDATE_INT) ||
            $team_size < 1 ||
            $team_size > 20
        ) {
            $error = "Team size must be between 1 and 20.";
        } else {

            try {
                // Insert project into database
                $sql = "INSERT INTO projects
                        (title, category, description, skills_needed,
                         team_size, status)
                        VALUES
                        (:title, :category, :description, :skills,
                         :team_size, 'pending')";

                $stmt = $conn->prepare($sql);

                $stmt->execute([
                    ':title' => $title,
                    ':category' => $category,
                    ':description' => $description,
                    ':skills' => $skills,
                    ':team_size' => $team_size
                ]);

                $success = "Your project idea has been submitted successfully! It is waiting for admin approval.";

                // Clear form after successful submission
                $title = "";
                $category = "";
                $description = "";
                $skills = "";
                $team_size = "";

                // Generate a new token
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

            } catch (PDOException $e) {
                $error = "Unable to submit your idea. Please try again.";
            }
        }
    }
}

// Function to safely display form values
function clean($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Submit Idea | IdeaVault</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #07152f, #0b1d3d, #102d58);
            color: white;
            min-height: 100vh;
        }

        /* Main layout */
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
            font-size: 26px;
            font-weight: bold;
            color: #00d9ff;
            margin-bottom: 40px;
            text-align: center;
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

        /* Form card */
        .form-card {
            max-width: 850px;
            margin: auto;
            padding: 35px;
            background: rgba(15, 39, 75, 0.75);
            border: 1px solid rgba(0, 195, 255, 0.25);
            border-radius: 18px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.25);
            backdrop-filter: blur(12px);
        }

        .form-card h2 {
            color: #00d9ff;
            margin-bottom: 10px;
            font-size: 23px;
        }

        .form-description {
            color: #a9c5e8;
            margin-bottom: 28px;
            line-height: 1.6;
        }

        .form-group {
            margin-bottom: 22px;
        }

        .form-group label {
            display: block;
            margin-bottom: 9px;
            color: #e2efff;
            font-size: 15px;
            font-weight: bold;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 14px;
            border-radius: 9px;
            border: 1px solid #31547c;
            background: #0a1c38;
            color: white;
            outline: none;
            font-size: 14px;
            transition: 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #00d9ff;
            box-shadow: 0 0 10px rgba(0, 217, 255, 0.25);
        }

        .form-group input::placeholder,
        .form-group textarea::placeholder {
            color: #7895b8;
        }

        .form-group select option {
            background: #0a1c38;
            color: white;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 130px;
        }

        .hint {
            display: block;
            margin-top: 7px;
            color: #8faed1;
            font-size: 12px;
        }

        /* Buttons */
        .button-group {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .submit-btn {
            background: linear-gradient(90deg, #0879d1, #00c8f5);
            color: white;
            border: none;
            padding: 14px 28px;
            border-radius: 9px;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 20px rgba(0, 200, 255, 0.35);
        }

        .reset-btn {
            background: transparent;
            border: 1px solid #52749b;
            color: #c7dcf5;
            padding: 14px 28px;
            border-radius: 9px;
            cursor: pointer;
            font-size: 15px;
        }

        .reset-btn:hover {
            background: rgba(255, 255, 255, 0.08);
        }

        /* Messages */
        .message {
            padding: 14px;
            margin-bottom: 22px;
            border-radius: 9px;
            font-size: 14px;
            line-height: 1.5;
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

            .form-card {
                padding: 22px;
            }

            .button-group {
                flex-direction: column;
            }

            .submit-btn,
            .reset-btn {
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
            <a href="explore-ideas.php">Explore Ideas</a>
            <a href="submit-idea.php" class="active">Submit Idea</a>
            <a href="find-team.php">Find Team</a>
            <a href="my-projects.php">My Projects</a>
            <a href="project-dashboard.php">Project Dashboard</a>
            <a href="admin-dashboard.php">Admin Dashboard</a>
        </nav>

    </aside>

    <!-- Main page content -->
    <main class="main-content">

        <div class="top-heading">
            <h1>Submit Your Idea 💡</h1>
            <p>Share your creativity, find collaborators and turn your idea into reality.</p>
        </div>

        <div class="form-card">

            <h2>Project Information</h2>

            <p class="form-description">
                Have an innovative idea? Fill in the details below and
                share it with the IdeaVault community.
            </p>

            <!-- Success message -->
            <?php if ($success != ""): ?>
                <div class="message success">
                    <?php echo clean($success); ?>
                </div>
            <?php endif; ?>

            <!-- Error message -->
            <?php if ($error != ""): ?>
                <div class="message error">
                    <?php echo clean($error); ?>
                </div>
            <?php endif; ?>

            <!-- Submit idea form -->
            <form method="POST" action="submit-idea.php" id="ideaForm">

                <input type="hidden" name="csrf_token"
                       value="<?php echo clean($_SESSION['csrf_token']); ?>">

                <div class="form-group">
                    <label for="title">Project Title *</label>

                    <input
                        type="text"
                        id="title"
                        name="title"
                        placeholder="Enter your project title"
                        maxlength="150"
                        value="<?php echo clean($title); ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="category">Project Category *</label>

                    <select id="category" name="category" required>
                        <option value="">-- Select Category --</option>

                        <?php
                        $categories = [
                            "Web Development",
                            "App Development",
                            "Artificial Intelligence",
                            "Machine Learning",
                            "IoT",
                            "Cyber Security",
                            "Other"
                        ];

                        foreach ($categories as $cat) {
                            $selected = ($category == $cat) ? "selected" : "";
                            echo "<option value='" . clean($cat) . "' $selected>"
                                 . clean($cat) . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="description">Project Description *</label>

                    <textarea
                        id="description"
                        name="description"
                        placeholder="Explain your project idea, its purpose and how it will work..."
                        minlength="20"
                        required
                    ><?php echo clean($description); ?></textarea>

                    <span class="hint">
                        Write at least 20 characters describing your idea.
                    </span>
                </div>

                <div class="form-group">
                    <label for="skills">Skills Required *</label>

                    <input
                        type="text"
                        id="skills"
                        name="skills"
                        placeholder="Example: HTML, CSS, PHP, MySQL, JavaScript"
                        maxlength="255"
                        value="<?php echo clean($skills); ?>"
                        required
                    >

                    <span class="hint">
                        Mention the technical skills needed for this project.
                    </span>
                </div>

                <div class="form-group">
                    <label for="team_size">Required Team Size *</label>

                    <input
                        type="number"
                        id="team_size"
                        name="team_size"
                        placeholder="Enter number of team members"
                        min="1"
                        max="20"
                        value="<?php echo clean($team_size); ?>"
                        required
                    >
                </div>

                <div class="button-group">

                    <button type="submit" class="submit-btn">
                        Submit Idea
                    </button>

                    <button type="reset" class="reset-btn">
                        Clear Form
                    </button>

                </div>

            </form>

        </div>

        <div class="footer">
            © 2026 IdeaVault | Connect • Collaborate • Create
        </div>

    </main>

</div>

<!-- JavaScript validation -->
<script>
    document.getElementById("ideaForm").addEventListener("submit", function(event) {

        let title = document.getElementById("title").value.trim();
        let description = document.getElementById("description").value.trim();
        let skills = document.getElementById("skills").value.trim();
        let teamSize = Number(document.getElementById("team_size").value);

        if (title === "" || description === "" || skills === "") {
            alert("Please fill in all required fields.");
            event.preventDefault();
            return;
        }

        if (description.length < 20) {
            alert("Please write at least 20 characters in the description.");
            event.preventDefault();
            return;
        }

        if (!Number.isInteger(teamSize) || teamSize < 1 || teamSize > 20) {
            alert("Team size must be between 1 and 20.");
            event.preventDefault();
            return;
        }

        // Confirmation before submitting
        if (!confirm("Are you sure you want to submit this project idea?")) {
            event.preventDefault();
        }
    });
</script>

</body>
</html>
