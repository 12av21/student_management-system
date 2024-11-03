<?php
session_start();
include '../config.php'; // Include database connection

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch report data
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <link rel="stylesheet" href="styles.css"> <!-- Include your CSS here -->
</head>
<body>

<nav>
    <h1>Reports</h1>
</nav>

<div class="container">
    <h2>Reports</h2>
    <!-- Display report data here -->
</div>

</body>
</html>
