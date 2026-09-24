<?php
// Sample project ideas
$ideas = [
    [
        "title" => "Smart Attendance System",
        "description" => "An attendance system using face recognition to automatically mark student attendance.",
        "category" => "AI & ML",
        "difficulty" => "Intermediate",
        "technologies" => ["Python", "OpenCV", "MySQL"],
        "members" => 3
    ],
    [
        "title" => "Campus Lost & Found",
        "description" => "A platform where students can report lost items and help others find their belongings.",
        "category" => "Web Development",
        "difficulty" => "Beginner",
        "technologies" => ["HTML", "CSS", "PHP", "MySQL"],
        "members" => 4
    ],
    [
        "title" => "Student Expense Tracker",
        "description" => "A simple application to track daily expenses and manage monthly student budgets.",
        "category" => "Finance",
        "difficulty" => "Beginner",
        "technologies" => ["HTML", "JavaScript", "PHP"],
        "members" => 2
    ],
    [
        "title" => "Smart Study Planner",
        "description" => "Helps students organize subjects, assignments, deadlines and study schedules.",
        "category" => "Education",
        "difficulty" => "Intermediate",
        "technologies" => ["Bootstrap", "JavaScript", "PHP"],
        "members" => 3
    ],
    [
        "title" => "EcoTrack",
        "description" => "A platform that helps students track their daily environmental impact and activities.",
        "category" => "Environment",
        "difficulty" => "Intermediate",
        "technologies" => ["HTML", "CSS", "JavaScript"],
        "members" => 4
    ],
    [
        "title" => "AI Resume Analyzer",
        "description" => "Analyzes resumes and provides suggestions to improve skills and job compatibility.",
        "category" => "AI & ML",
        "difficulty" => "Advanced",
        "technologies" => ["Python", "AI", "NLP"],
        "members" => 4
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Explore Ideas | IdeaVault</title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: linear-gradient(135deg, #061a46, #062b6f, #06458e);
            color: white;
            min-height: 100vh;
        }

        /* NAVBAR */

        .navbar-custom {
            background: rgba(4, 20, 55, 0.95);
            border-bottom: 1px solid rgba(56, 189, 248, 0.35);
            padding: 14px 5%;
        }

        .brand {
            font-size: 25px;
            font-weight: bold;
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .brand-icon {
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, #20cfff, #1677ff);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 21px;
        }

        .nav-link {
            color: #dceeff !important;
            font-weight: 500;
            margin: 0 10px;
        }

        .nav-link:hover {
            color: #28d7ff !important;
        }

        .login-btn {
            border: 1px solid #3caeff;
            color: white;
            padding: 8px 18px;
            border-radius: 8px;
            text-decoration: none;
            margin-right: 8px;
        }

        .register-btn {
            background: linear-gradient(135deg, #24cfff, #1685ed);
            color: white;
            padding: 9px 18px;
            border-radius: 8px;
            text-decoration: none;
        }

        /* PAGE HEADER */

        .page-header {
            padding: 55px 7% 30px;
        }

        .small-title {
            color: #28d7ff;
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .page-header h1 {
            font-size: 42px;
            font-weight: 700;
            margin-top: 10px;
        }

        .page-header p {
            color: #c5ddf5;
            max-width: 650px;
            font-size: 17px;
        }

        /* SEARCH AREA */

        .search-section {
            padding: 0 7% 35px;
        }

        .search-box {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(76, 190, 255, 0.35);
            border-radius: 15px;
            padding: 20px;
            backdrop-filter: blur(10px);
        }

        .search-input {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(120,200,255,0.3);
            color: white;
            border-radius: 9px;
            padding: 12px 15px;
        }

        .search-input::placeholder {
            color: #aac7e4;
        }

        .search-input:focus {
            background: rgba(255,255,255,0.1);
            color: white;
            border-color: #27d5ff;
            box-shadow: 0 0 10px rgba(39,213,255,0.25);
        }

        .filter-btn {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(120,200,255,0.3);
            color: white;
            border-radius: 9px;
            padding: 11px 18px;
        }

        .filter-btn:hover {
            background: #168fe9;
            color: white;
        }

        /* IDEA CARDS */

        .ideas-section {
            padding: 0 7% 60px;
        }

        .idea-card {
            height: 100%;
            background: rgba(7, 32, 77, 0.82);
            border: 1px solid rgba(65, 180, 255, 0.28);
            border-radius: 17px;
            padding: 23px;
            transition: 0.3s;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .idea-card:hover {
            transform: translateY(-6px);
            border-color: #24cfff;
            box-shadow: 0 10px 30px rgba(20, 194, 255, 0.18);
        }

        .category {
            display: inline-block;
            background: rgba(32, 202, 255, 0.13);
            color: #35d9ff;
            border: 1px solid rgba(53,217,255,0.3);
            padding: 5px 11px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 14px;
        }

        .idea-card h3 {
            font-size: 21px;
            margin-bottom: 10px;
            color: white;
        }

        .idea-card p {
            color: #bdd3eb;
            font-size: 14px;
            line-height: 1.6;
            min-height: 67px;
        }

        .difficulty {
            font-size: 12px;
            color: #8edbff;
            margin-bottom: 13px;
        }

        .tech-tag {
            display: inline-block;
            background: #092d60;
            color: #cdeaff;
            padding: 5px 9px;
            border-radius: 6px;
            font-size: 11px;
            margin: 2px;
        }

        .card-bottom {
            border-top: 1px solid rgba(255,255,255,0.1);
            margin-top: 18px;
            padding-top: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .members {
            color: #a9c8e8;
            font-size: 13px;
        }

        .view-btn {
            background: linear-gradient(135deg, #25cdf5, #147de5);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 7px;
            font-size: 13px;
            text-decoration: none;
        }

        .view-btn:hover {
            color: white;
            opacity: 0.9;
        }

        /* FOOTER */

        footer {
            background: rgba(3, 16, 43, 0.9);
            border-top: 1px solid rgba(70,190,255,0.2);
            padding: 25px;
            text-align: center;
            color: #9db8d5;
            font-size: 13px;
        }

        /* MOBILE */

        @media(max-width: 768px) {

            .page-header {
                padding: 40px 5% 25px;
            }

            .page-header h1 {
                font-size: 32px;
            }

            .search-section,
            .ideas-section {
                padding-left: 5%;
                padding-right: 5%;
            }

            .login-btn,
            .register-btn {
                display: inline-block;
                margin-top: 8px;
            }
        }

    </style>
</head>

<body>

<!-- NAVBAR -->

<nav class="navbar navbar-expand-lg navbar-custom">

    <div class="container-fluid">

        <a href="index.php" class="brand">
            <span class="brand-icon">💡</span>
            IdeaVault
        </a>

        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">

            <ul class="navbar-nav mx-auto">

                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="explore-ideas.php">
                        Explore Ideas
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="submit-idea.php">
                        Submit Idea
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="find-team.php">
                        Find Team
                    </a>
                </li>

            </ul>

            <div>
                <a href="login.php" class="login-btn">Login</a>
                <a href="register.php" class="register-btn">Register</a>
            </div>

        </div>

    </div>

</nav>


<!-- PAGE HEADER -->

<section class="page-header">

    <div class="small-title">
        Discover • Connect • Create
    </div>

    <h1>Explore Project Ideas</h1>

    <p>
        Discover innovative ideas shared by students, find projects
        that match your interests and connect with people who can
        help bring those ideas to life.
    </p>

</section>


<!-- SEARCH + FILTER -->

<section class="search-section">

    <div class="search-box">

        <div class="row g-3">

            <div class="col-lg-7">

                <input
                    type="text"
                    id="searchInput"
                    class="form-control search-input"
                    placeholder="🔍 Search project ideas..."
                    onkeyup="searchIdeas()">

            </div>

            <div class="col-lg-3">

                <select
                    id="categoryFilter"
                    class="form-select search-input"
                    onchange="filterIdeas()">

                    <option value="all">All Categories</option>
                    <option value="Web Development">Web Development</option>
                    <option value="AI & ML">AI & ML</option>
                    <option value="Education">Education</option>
                    <option value="Finance">Finance</option>
                    <option value="Environment">Environment</option>

                </select>

            </div>

            <div class="col-lg-2">

                <button
                    class="filter-btn w-100"
                    onclick="resetFilters()">

                    Reset

                </button>

            </div>

        </div>

    </div>

</section>


<!-- IDEA CARDS -->

<section class="ideas-section">

    <div class="row g-4" id="ideasContainer">

        <?php foreach ($ideas as $idea): ?>

        <div
            class="col-md-6 col-lg-4 idea-item"
            data-title="<?php echo strtolower($idea['title']); ?>"
            data-category="<?php echo $idea['category']; ?>">

            <div class="idea-card">

                <span class="category">
                    <?php echo $idea['category']; ?>
                </span>

                <h3>
                    <?php echo $idea['title']; ?>
                </h3>

                <p>
                    <?php echo $idea['description']; ?>
                </p>

                <div class="difficulty">
                    ⚡ Difficulty:
                    <strong><?php echo $idea['difficulty']; ?></strong>
                </div>

                <div>

                    <?php foreach ($idea['technologies'] as $tech): ?>

                        <span class="tech-tag">
                            <?php echo $tech; ?>
                        </span>

                    <?php endforeach; ?>

                </div>

                <div class="card-bottom">

                    <span class="members">
                        👥 <?php echo $idea['members']; ?> members needed
                    </span>

                    <a href="project-details.php" class="view-btn">
                        View Idea
                    </a>

                </div>

            </div>

        </div>

        <?php endforeach; ?>

    </div>

</section>


<!-- FOOTER -->

<footer>

    © 2026 IdeaVault • Where Ideas Find Their Team

</footer>


<!-- BOOTSTRAP JS -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


<script>

function searchIdeas() {

    let search =
        document.getElementById("searchInput")
        .value
        .toLowerCase();

    let cards =
        document.querySelectorAll(".idea-item");

    cards.forEach(function(card) {

        let title =
            card.getAttribute("data-title");

        if (title.includes(search)) {
            card.style.display = "";
        } else {
            card.style.display = "none";
        }

    });

}


function filterIdeas() {

    let category =
        document.getElementById("categoryFilter").value;

    let cards =
        document.querySelectorAll(".idea-item");

    cards.forEach(function(card) {

        let cardCategory =
            card.getAttribute("data-category");

        if (category === "all" ||
            cardCategory === category) {

            card.style.display = "";

        } else {

            card.style.display = "none";

        }

    });

}


function resetFilters() {

    document.getElementById("searchInput").value = "";

    document.getElementById("categoryFilter").value = "all";

    let cards =
        document.querySelectorAll(".idea-item");

    cards.forEach(function(card) {
        card.style.display = "";
    });

}

</script>

</body>
</html>