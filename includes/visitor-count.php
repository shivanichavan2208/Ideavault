
<?php

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
require_once __DIR__ . '/db.php';

try {

    // Count one visit per session
    if (!isset($_SESSION['ideavault_visit_counted'])) {

        $update = $conn->prepare(
            "UPDATE website_visits
             SET total_visits = total_visits + 1
             WHERE id = 1"
        );

        $update->execute();

        $_SESSION['ideavault_visit_counted'] = true;
    }

    // Fetch total visitors
    $query = $conn->query(
        "SELECT total_visits
         FROM website_visits
         WHERE id = 1"
    );

    $totalVisitors = (int) $query->fetchColumn();

} catch (PDOException $e) {

    $totalVisitors = 0;

}

?>
