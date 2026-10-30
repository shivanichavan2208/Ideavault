
<?php
// Identify the currently opened page
$currentPage = basename($_SERVER['PHP_SELF']);

// Navigation menu items
$menuItems = [
    "index.html" => ["🏠", "Home"],
    "dashboard.html" => ["📊", "Dashboard"],
    "explore-ideas.php" => ["💡", "Explore Ideas"],
    "submit-idea.php" => ["➕", "Submit Idea"],
    "find-team.php" => ["👥", "Find Team"],
    "my-projects.php" => ["📁", "My Projects"],
    "profile.html" => ["👤", "Profile"],
    "project-dashboard.php" => ["📈", "Project Dashboard"],
    "admin-dashboard.php" => ["⚙️", "Admin Dashboard"]
];
?>

<style>
.sidebar {
    width: 245px;
    min-width: 245px;
    min-height: 100vh;
    padding: 25px 18px;
    background: rgba(5, 18, 38, 0.97);
    border-right: 1px solid rgba(34, 211, 238, 0.2);
}

.sidebar-logo {
    color: #22d3ee;
    font-size: 26px;
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
    font-size: 15px;
    transition: 0.3s;
}

.sidebar a:hover,
.sidebar a.active {
    background: linear-gradient(90deg, #087da8, #075985);
    color: white;
    transform: translateX(4px);
}

@media (max-width: 650px) {
    .sidebar {
        width: 100%;
        min-width: 100%;
        min-height: auto;
    }
}
</style>

<aside class="sidebar">

    <div class="sidebar-logo">
        IdeaVault
    </div>

    <?php foreach ($menuItems as $file => $item): ?>

        <a href="<?php echo htmlspecialchars($file); ?>"
           class="<?php echo ($currentPage === $file) ? 'active' : ''; ?>">

            <?php echo $item[0] . " " . htmlspecialchars($item[1]); ?>

        </a>

    <?php endforeach; ?>

</aside>
