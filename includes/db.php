
<?php

// Database connection details
$host = "localhost";
$dbname = "ideavault";
$username = "root";
$password = "";

try {

    // Create database connection using PDO
    $conn = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    // Enable error reporting
    $conn->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    // Fetch database results as associative arrays
    $conn->setAttribute(
        PDO::ATTR_DEFAULT_FETCH_MODE,
        PDO::FETCH_ASSOC
    );

} catch (PDOException $e) {

    // Display connection error during development
    die("Database Connection Failed: " . $e->getMessage());

}

?>
