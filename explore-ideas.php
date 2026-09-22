<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Explore Ideas - IdeaVault</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f5f7fb;
            font-family: Arial, sans-serif;
        }

        .navbar {
            background-color: #243b55;
        }

        .navbar-brand {
            color: white;
            font-size: 24px;
            font-weight: bold;
        }

        .navbar-brand:hover {
            color: #ffffff;
        }

        .nav-link {
            color: white !important;
        }

        .page-header {
            text-align: center;
            padding: 40px 20px 25px;
        }

        .page-header h1 {
            color: #243b55;
            font-weight: bold;
        }

        .page-header p {
            color: #666;
        }

        .search-box {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }

        .idea-card {
            background-color: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            height: 100%;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: 0.3s;
        }

        .idea-card:hover {
            transform: translateY(-5px);
        }

        .idea-card h4 {
            color: #243b55;
            font-weight: bold;
        }

        .category {
            display: inline-block;
            background-color: #e8f0fe;
            color: #1a5dab;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 13px;
            margin-bottom: 10px;
        }

        .technology {
            background-color: #f1f1f1;
            padding: 5px 9px;
            border-radius: 5px;
            font-size: 12px;
            display: inline-block;
            margin: 3px;
        }

        .difficulty {
            color: #555;
            font-size: 14px;
        }

        .join-btn {
            background-color: #243b55;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 6px;
        }

        .join-btn:hover {
            background-color: #162b40;
            color: white;
        }

        .details-btn {
            border: 1px solid #243b55;
            color: #243b55;
            padding: 7px 14px;
            border-radius: 6px;
            text-decoration: none;
        }

        .details-btn:hover {
            background-color: #243b55;
            color: white;
        }

        footer {
            background-color: #243b55;
            color: white;
            text-align: center;
            padding: 15px;
            margin-top: 40px;
        }
    </style>
</head>

<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg">
    <div class="container">

        <a class="navbar-brand" href="index.php">
            💡 IdeaVault
        </a>

        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMenu">

            <ul class="navbar-nav ms-auto">

                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link active" href="explore.php">
                        Explore Ideas
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="submit.php">
                        Submit Idea
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">
                        Dashboard
                    </a>
                </li>

            </ul>

        </div>
    </div>
</nav>


<!-- Page Header -->
<div class="page-header">

    <h1>Explore Project Ideas 💡</h1>

    <p>
        Discover interesting project ideas and find students
        who share your interests.
    </p>

</div>


<div class="container">

    <!-- Search and Filter -->
    <div class="search-box">

        <div class="row g-3">

            <div class="col-md-5">
                <input type="text"
                       id="searchInput"
                       class="form-control"
                       placeholder="🔍 Search project ideas...">
            </div>

            <div class="col-md-3">

                <select id="categoryFilter" class="form-select">

                    <option value="">All Categories</option>
                    <option value="Web Development">Web Development</option>
                    <option value="AI">AI & ML</option>
                    <option value="IoT">IoT</option>
                    <option value="Mobile App">Mobile App</option>

                </select>

            </div>

            <div class="col-md-3">

                <select id="difficultyFilter" class="form-select">

                    <option value="">All Levels</option>
                    <option value="Beginner">Beginner</option>
                    <option value="Intermediate">Intermediate</option>
                    <option value="Advanced">Advanced</option>

                </select>

            </div>

            <div class="col-md-1">

                <button class="btn btn-secondary w-100"
                        onclick="resetFilters()">
                    Reset
                </button>

            </div>

        </div>

    </div>


    <!-- Project Ideas -->

    <div class="row" id="ideaContainer">


        <!-- Idea 1 -->
        <div class="col-md-6 col-lg-4 idea-item">

            <div class="idea-card">

                <span class="category">Web Development</span>

                <h4>📚 Study Buddy</h4>

                <p>
                    A website where students can share notes,
                    create study groups and manage their study tasks.
                </p>

                <p class="difficulty">
                    <b>Difficulty:</b> Beginner
                </p>

                <p>
                    <b>Skills:</b>
                </p>

                <span class="technology">HTML</span>
                <span class="technology">CSS</span>
                <span class="technology">PHP</span>
                <span class="technology">MySQL</span>

                <p class="mt-3">
                    👥 <b>Team Members Needed:</b> 2
                </p>

                <div class="d-flex gap-2">

                    <a href="project-details.php?id=1"
                       class="details-btn">
                        View Details
                    </a>

                    <button class="join-btn">
                        Request to Join
                    </button>

                </div>

            </div>

        </div>


        <!-- Idea 2 -->
        <div class="col-md-6 col-lg-4 idea-item">

            <div class="idea-card">

                <span class="category">AI</span>

                <h4>🤖 Student Chatbot</h4>

                <p>
                    An AI chatbot that helps students get information
                    about college subjects, schedules and activities.
                </p>

                <p class="difficulty">
                    <b>Difficulty:</b> Intermediate
                </p>

                <p>
                    <b>Skills:</b>
                </p>

                <span class="technology">Python</span>
                <span class="technology">AI</span>
                <span class="technology">MySQL</span>

                <p class="mt-3">
                    👥 <b>Team Members Needed:</b> 3
                </p>

                <div class="d-flex gap-2">

                    <a href="project-details.php?id=2"
                       class="details-btn">
                        View Details
                    </a>

                    <button class="join-btn">
                        Request to Join
                    </button>

                </div>

            </div>

        </div>


        <!-- Idea 3 -->
        <div class="col-md-6 col-lg-4 idea-item">

            <div class="idea-card">

                <span class="category">IoT</span>

                <h4>💡 Smart Classroom</h4>

                <p>
                    A smart classroom system that automatically controls
                    lights and fans using sensors.
                </p>

                <p class="difficulty">
                    <b>Difficulty:</b> Intermediate
                </p>

                <p>
                    <b>Skills:</b>
                </p>

                <span class="technology">Arduino</span>
                <span class="technology">Sensors</span>
                <span class="technology">IoT</span>

                <p class="mt-3">
                    👥 <b>Team Members Needed:</b> 2
                </p>

                <div class="d-flex gap-2">

                    <a href="project-details.php?id=3"
                       class="details-btn">
                        View Details
                    </a>

                    <button class="join-btn">
                        Request to Join
                    </button>

                </div>

            </div>

        </div>


        <!-- Idea 4 -->
        <div class="col-md-6 col-lg-4 idea-item">

            <div class="idea-card">

                <span class="category">Mobile App</span>

                <h4>📱 Campus Connect</h4>

                <p>
                    A mobile application that helps students find
                    college events, clubs and announcements.
                </p>

                <p class="difficulty">
                    <b>Difficulty:</b> Beginner
                </p>

                <p>
                    <b>Skills:</b>
                </p>

                <span class="technology">Java</span>
                <span class="technology">Android</span>
                <span class="technology">Firebase</span>

                <p class="mt-3">
                    👥 <b>Team Members Needed:</b> 3
                </p>

                <div class="d-flex gap-2">

                    <a href="project-details.php?id=4"
                       class="details-btn">
                        View Details
                    </a>

                    <button class="join-btn">
                        Request to Join
                    </button>

                </div>

            </div>

        </div>


        <!-- Idea 5 -->
        <div class="col-md-6 col-lg-4 idea-item">

            <div class="idea-card">

                <span class="category">Web Development</span>

                <h4>🎓 Internship Finder</h4>

                <p>
                    A platform where students can find internships
                    based on their skills and interests.
                </p>

                <p class="difficulty">
                    <b>Difficulty:</b> Intermediate
                </p>

                <p>
                    <b>Skills:</b>
                </p>

                <span class="technology">HTML</span>
                <span class="technology">Bootstrap</span>
                <span class="technology">PHP</span>
                <span class="technology">MySQL</span>

                <p class="mt-3">
                    👥 <b>Team Members Needed:</b> 3
                </p>

                <div class="d-flex gap-2">

                    <a href="project-details.php?id=5"
                       class="details-btn">
                        View Details
                    </a>

                    <button class="join-btn">
                        Request to Join
                    </button>

                </div>

            </div>

        </div>


        <!-- Idea 6 -->
        <div class="col-md-6 col-lg-4 idea-item">

            <div class="idea-card">

                <span class="category">AI</span>

                <h4>🌱 Smart Plant Monitor</h4>

                <p>
                    A system that monitors soil moisture and helps
                    students take better care of plants.
                </p>

                <p class="difficulty">
                    <b>Difficulty:</b> Advanced
                </p>

                <p>
                    <b>Skills:</b>
                </p>

                <span class="technology">Arduino</span>
                <span class="technology">Python</span>
                <span class="technology">Sensors</span>

                <p class="mt-3">
                    👥 <b>Team Members Needed:</b> 2
                </p>

                <div class="d-flex gap-2">

                    <a href="project-details.php?id=6"
                       class="details-btn">
                        View Details
                    </a>

                    <button class="join-btn">
                        Request to Join
                    </button>

                </div>

            </div>

        </div>

    </div>

</div>


<!-- Footer -->

<footer>
    <p class="mb-0">
        © 2026 IdeaVault | Where Ideas Find Their Team 💡
    </p>
</footer>


<!-- JavaScript -->

<script>

    const searchInput = document.getElementById("searchInput");
    const categoryFilter = document.getElementById("categoryFilter");
    const difficultyFilter = document.getElementById("difficultyFilter");

    function filterIdeas() {

        let searchText =
            searchInput.value.toLowerCase();

        let category =
            categoryFilter.value;

        let difficulty =
            difficultyFilter.value;

        let ideas =
            document.querySelectorAll(".idea-item");


        ideas.forEach(function(idea) {

            let text =
                idea.innerText.toLowerCase();

            let categoryMatch =
                category === "" ||
                text.includes(category.toLowerCase());

            let difficultyMatch =
                difficulty === "" ||
                text.includes(difficulty.toLowerCase());

            let searchMatch =
                text.includes(searchText);


            if (categoryMatch &&
                difficultyMatch &&
                searchMatch) {

                idea.style.display = "block";

            } else {

                idea.style.display = "none";

            }

        });

    }


    searchInput.addEventListener("keyup", filterIdeas);

    categoryFilter.addEventListener("change", filterIdeas);

    difficultyFilter.addEventListener("change", filterIdeas);


    function resetFilters() {

        searchInput.value = "";
        categoryFilter.value = "";
        difficultyFilter.value = "";

        filterIdeas();

    }

</script>


</body>
</html>