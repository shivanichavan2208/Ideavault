
<?php
session_start();

/* --------------------------------
   DATABASE CONNECTION
--------------------------------- */

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

/* --------------------------------
   SEARCH AND CATEGORY FILTER
--------------------------------- */

$search = trim($_GET['search'] ?? '');
$category = trim($_GET['category'] ?? '');

$sql = "SELECT * FROM projects WHERE status = 'approved'";
$params = [];

if ($search !== '') {
    $sql .= " AND (
        title LIKE :search1
        OR description LIKE :search2
        OR skills_needed LIKE :search3
        OR category LIKE :search4
    )";

    $keyword = "%" . $search . "%";

    $params[':search1'] = $keyword;
    $params[':search2'] = $keyword;
    $params[':search3'] = $keyword;
    $params[':search4'] = $keyword;
}

if ($category !== '') {
    $sql .= " AND category = :category";
    $params[':category'] = $category;
}

$sql .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);

$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* --------------------------------
   GET PROJECT CATEGORIES
--------------------------------- */

$categoryQuery = $conn->query(
    "SELECT DISTINCT category FROM projects
     WHERE status = 'approved'
     ORDER BY category ASC"
);

$categories = $categoryQuery->fetchAll(PDO::FETCH_COLUMN);

/* --------------------------------
   HELPER FUNCTION
--------------------------------- */

function clean($value) {
    return htmlspecialchars(
        $value ?? '',
        ENT_QUOTES,
        'UTF-8'
    );
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Explore Ideas | IdeaVault</title>

    <style>

        /* --------------------------------
           GENERAL STYLING
        --------------------------------- */

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Arial, sans-serif;
        }

        body {
            background: #061b49;
            color: #ffffff;
            min-height: 100vh;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        /* --------------------------------
           DASHBOARD LAYOUT
        --------------------------------- */

        .dashboard {
            display: flex;
            min-height: 100vh;
        }

        /* --------------------------------
           SIDEBAR
        --------------------------------- */

        .sidebar {
            width: 250px;
            min-width: 250px;
            background: linear-gradient(
                180deg,
                #0874c9,
                #064b9e
            );

            padding: 25px 18px;
            display: flex;
            flex-direction: column;
        }

        .logo {
            font-size: 23px;
            font-weight: bold;
            margin-bottom: 45px;
            padding-left: 8px;
        }

        .logo span {
            background: #16c9f5;
            padding: 9px;
            border-radius: 10px;
            margin-right: 7px;
        }

        .menu a {
            display: block;
            padding: 13px 15px;
            margin-bottom: 9px;
            border-radius: 10px;
            color: #d8ecff;
            font-size: 14px;
            transition: 0.3s;
        }

        .menu a:hover,
        .menu a.active {
            background: #16aee9;
            color: white;
            box-shadow: 0 4px 15px #001b4560;
        }

        .sidebar-bottom {
            margin-top: auto;
            padding-top: 35px;
        }

        .sidebar-bottom a {
            display: block;
            padding: 10px;
            font-size: 13px;
            color: #d5eaff;
        }

        /* --------------------------------
           MAIN CONTENT
        --------------------------------- */

        .main-content {
            flex: 1;
            padding: 35px 40px;
            overflow: hidden;
            background:
                radial-gradient(
                    circle at top right,
                    #0872c9 0%,
                    transparent 35%
                ),
                linear-gradient(
                    135deg,
                    #08245b,
                    #061943
                );
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #3679bb;
            padding-bottom: 22px;
            margin-bottom: 35px;
        }

        .topbar small {
            color: #39d7ff;
            font-size: 11px;
            letter-spacing: 2px;
        }

        .topbar h2 {
            font-size: 24px;
            margin-top: 5px;
        }

        .profile {
            background: #0878cb;
            padding: 11px 18px;
            border-radius: 25px;
            font-size: 13px;
        }

        /* --------------------------------
           PAGE INTRODUCTION
        --------------------------------- */

        .intro {
            margin-bottom: 28px;
        }

        .tag {
            display: inline-block;
            background: #087bc4;
            border: 1px solid #36caff;
            color: #8beaff;
            padding: 7px 15px;
            border-radius: 25px;
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 17px;
        }

        .intro h1 {
            font-size: 34px;
            margin-bottom: 12px;
        }

        .intro h1 span {
            color: #20c9f5;
        }

        .intro p {
            color: #b7d5f5;
            line-height: 1.7;
            font-size: 14px;
        }

        /* --------------------------------
           SEARCH SECTION
        --------------------------------- */

        .search-section {
            background: #0c3977;
            border: 1px solid #297cc0;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 25px;
        }

        .search-form {
            display: flex;
            gap: 12px;
        }

        .search-input,
        .category-select {
            background: #0755a5;
            color: white;
            border: 1px solid #318bd0;
            padding: 14px;
            border-radius: 9px;
            outline: none;
            font-size: 14px;
        }

        .search-input {
            flex: 1;
            min-width: 0;
        }

        .search-input::placeholder {
            color: #c1dfff;
        }

        .category-select {
            min-width: 175px;
            cursor: pointer;
        }

        .category-select option {
            background: #07336c;
        }

        .search-btn {
            background: linear-gradient(
                90deg,
                #16bce9,
                #19d4ef
            );

            color: white;
            border: none;
            padding: 13px 24px;
            border-radius: 9px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        .search-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px #16c9f566;
        }

        .clear-btn {
            display: inline-block;
            color: #8deaff;
            margin-top: 12px;
            font-size: 13px;
        }

        /* --------------------------------
           RESULTS HEADER
        --------------------------------- */

        .results-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 30px 0 20px;
        }

        .results-header h2 {
            font-size: 21px;
        }

        .results-count {
            color: #76dfff;
            font-size: 13px;
        }

        /* --------------------------------
           PROJECT CARDS
        --------------------------------- */

        .project-grid {
            display: grid;
            grid-template-columns: repeat(
                auto-fit,
                minmax(260px, 1fr)
            );

            gap: 22px;
        }

        .project-card {
            background: linear-gradient(
                145deg,
                #1057a2,
                #0b3474
            );

            border: 1px solid #3289d0;
            border-radius: 15px;
            padding: 22px;
            transition: 0.3s;
            box-shadow: 0 7px 20px #00133735;
        }

        .project-card:hover {
            transform: translateY(-6px);
            border-color: #25d0fa;
            box-shadow: 0 10px 25px #00a9ff25;
        }

        .project-category {
            display: inline-block;
            background: #087bc5;
            color: #a4edff;
            border: 1px solid #31b9eb;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 11px;
            margin-bottom: 17px;
        }

        .project-card h3 {
            font-size: 19px;
            margin-bottom: 12px;
            color: white;
        }

        .project-description {
            font-size: 13px;
            color: #c4def8;
            line-height: 1.7;
            margin-bottom: 18px;
            min-height: 65px;
        }

        .skills-title {
            color: #5bdcff;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .skills {
            display: flex;
            flex-wrap: wrap;
            gap: 7px;
            margin-bottom: 20px;
        }

        .skill {
            background: #07356e;
            border: 1px solid #286db0;
            color: #b8e8ff;
            padding: 6px 10px;
            border-radius: 6px;
            font-size: 11px;
        }

        .project-info {
            border-top: 1px solid #3474ad;
            padding-top: 15px;
            display: flex;
            justify-content: space-between;
            color: #b6d9fa;
            font-size: 12px;
            margin-bottom: 17px;
        }

        .view-btn {
            display: block;
            text-align: center;
            padding: 11px;
            border-radius: 8px;
            background: linear-gradient(
                90deg,
                #12b8e9,
                #19d5ef
            );

            color: white;
            font-weight: bold;
            font-size: 13px;
            transition: 0.3s;
        }

        .view-btn:hover {
            background: #078dc8;
        }

        /* --------------------------------
           EMPTY RESULTS
        --------------------------------- */

        .empty-state {
            text-align: center;
            background: #0d3978;
            border: 1px dashed #398bd0;
            border-radius: 15px;
            padding: 65px 20px;
            margin-top: 20px;
        }

        .empty-icon {
            font-size: 40px;
            margin-bottom: 18px;
        }

        .empty-state h3 {
            margin-bottom: 10px;
        }

        .empty-state p {
            color: #bad7f5;
            font-size: 13px;
        }

        .empty-state a {
            display: inline-block;
            margin-top: 20px;
            background: #16bde9;
            padding: 11px 20px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: bold;
        }

        /* --------------------------------
           FOOTER
        --------------------------------- */

        footer {
            border-top: 1px solid #28649d;
            margin-top: 55px;
            padding: 20px;
            text-align: center;
            color: #9fc5e9;
            font-size: 12px;
        }

        /* --------------------------------
           RESPONSIVE DESIGN
        --------------------------------- */

        @media(max-width: 850px) {

            .sidebar {
                width: 190px;
                min-width: 190px;
            }

            .main-content {
                padding: 25px 20px;
            }

            .search-form {
                flex-wrap: wrap;
            }

            .search-input {
                flex-basis: 100%;
            }

        }

        @media(max-width: 600px) {

            .dashboard {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                min-width: 100%;
                padding: 15px;
            }

            .logo {
                margin-bottom: 15px;
            }

            .menu {
                display: flex;
                overflow-x: auto;
                gap: 5px;
            }

            .menu a {
                white-space: nowrap;
                margin-bottom: 0;
            }

            .sidebar-bottom {
                display: none;
            }

            .main-content {
                padding: 22px 15px;
            }

            .intro h1 {
                font-size: 27px;
            }

            .category-select,
            .search-btn {
                width: 100%;
            }

            .results-header {
                align-items: flex-start;
                gap: 10px;
                flex-direction: column;
            }

        }

    </style>

</head>

<body>

<div class="dashboard">

    <!-- SIDEBAR -->

    <aside class="sidebar">

        <div class="logo">
            <span>IV</span> IdeaVault
        </div>

        <nav class="menu">

            <a href="dashboard.html">
                ◉ Dashboard
            </a>

            <a href="dashboard.html#profile">
                ◯ My Profile
            </a>

            <a href="submit-idea.php">
                ＋ Share Idea
            </a>

            <a href="explore-ideas.php" class="active">
                ⌕ Explore Ideas
            </a>

            <a href="find-team.php">
                ♧ Find Team
            </a>

            <a href="my-projects.php">
                ▣ My Projects
            </a>

        </nav>

        <div class="sidebar-bottom">

            <a href="index.html">
                ← Website
            </a>

            <a href="login.html">
                ↪ Logout
            </a>

        </div>

    </aside>


    <!-- MAIN CONTENT -->

    <main class="main-content">

        <!-- TOP BAR -->

        <header class="topbar">

            <div>
                <small>DISCOVER</small>
                <h2>Explore Ideas</h2>
            </div>

            <div class="profile">
                Student Workspace
            </div>

        </header>


        <!-- INTRODUCTION -->

        <section class="intro">

            <span class="tag">
                DISCOVER NEW OPPORTUNITIES
            </span>

            <h1>
                Explore Project
                <span>Ideas.</span>
            </h1>

            <p>
                Discover innovative projects shared by students.
                Find ideas that match your interests, explore new
                technologies and connect with future teammates.
            </p>

        </section>


        <!-- SEARCH FORM -->

        <section class="search-section">

            <form action="explore-ideas.php"
                  method="GET"
                  class="search-form">

                <input
                    type="text"
                    name="search"
                    class="search-input"
                    placeholder="⌕ Search by project name, category or skill..."
                    value="<?php echo clean($search); ?>"
                >

                <select
                    name="category"
                    class="category-select">

                    <option value="">All Categories</option>

                    <?php foreach ($categories as $cat): ?>

                        <option
                            value="<?php echo clean($cat); ?>"
                            <?php
                            echo ($category === $cat)
                                ? 'selected'
                                : '';
                            ?>
                        >
                            <?php echo clean($cat); ?>
                        </option>

                    <?php endforeach; ?>

                </select>

                <button type="submit" class="search-btn">
                    Search Ideas
                </button>

            </form>

            <a href="explore-ideas.php" class="clear-btn">
                ↻ Clear Search
            </a>

        </section>


        <!-- PROJECT RESULTS -->

        <section>

            <div class="results-header">

                <h2>Available Project Ideas</h2>

                <span class="results-count">
                    <?php echo count($projects); ?>
                    Project(s) Found
                </span>

            </div>


            <?php if (count($projects) > 0): ?>

                <div class="project-grid">

                    <?php foreach ($projects as $project): ?>

                        <div class="project-card">

                            <span class="project-category">
                                <?php echo clean($project['category']); ?>
                            </span>

                            <h3>
                                <?php echo clean($project['title']); ?>
                            </h3>

                            <p class="project-description">

                                <?php
                                $description = $project['description'];

                                if (strlen($description) > 130) {
                                    echo clean(substr($description, 0, 130))
                                         . "...";
                                } else {
                                    echo clean($description);
                                }
                                ?>

                            </p>

                            <div class="skills-title">
                                SKILLS REQUIRED
                            </div>

                            <div class="skills">

                                <?php

                                $skills = explode(
                                    ',',
                                    $project['skills_needed'] ?? ''
                                );

                                foreach ($skills as $skill):

                                    if (trim($skill) !== ''):

                                ?>

                                    <span class="skill">
                                        <?php echo clean(trim($skill)); ?>
                                    </span>

                                <?php
                                    endif;
                                endforeach;
                                ?>

                            </div>


                            <div class="project-info">

                                <span>
                                    Team Size:
                                    <?php
                                    echo clean($project['team_size']);
                                    ?>
                                </span>

                                <span>
                                    <?php
                                    echo !empty($project['created_at'])
                                        ? date(
                                            'd M Y',
                                            strtotime($project['created_at'])
                                        )
                                        : '';
                                    ?>
                                </span>

                            </div>


                            <a
                                href="project-details.php?id=<?php
                                    echo (int)$project['id'];
                                ?>"
                                class="view-btn">

                                View Project Details →

                            </a>

                        </div>

                    <?php endforeach; ?>

                </div>


            <?php else: ?>

                <!-- NO PROJECT FOUND -->

                <div class="empty-state">

                    <div class="empty-icon">
                        🔍
                    </div>

                    <h3>No Project Ideas Found</h3>

                    <p>
                        We couldn't find any projects matching
                        your search. Try another keyword or category.
                    </p>

                    <a href="explore-ideas.php">
                        View All Projects
                    </a>

                </div>

            <?php endif; ?>

        </section>


        <!-- FOOTER -->

        <footer>

            <p>
                © 2026 IdeaVault |
                Student Project Collaboration Platform
            </p>

        </footer>

    </main>

</div>

</body>
</html>
