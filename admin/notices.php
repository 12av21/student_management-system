<?php
session_start();
include '../config.php'; // Include database connection

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle notice management (add, edit, delete)
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Notices</title>
    <link rel="stylesheet" href="styles.css"> <!-- Include your CSS here -->
</head>
<body>

<nav>
    <h1>Manage Notices</h1>
</nav>

<div class="container">
    <h2>Notices</h2>
    <!-- Form to add/edit notices -->
    <form action="add_notice.php" method="POST">
        <label for="notice">Notice:</label>
        <textarea id="notice" name="notice" required></textarea>

        <input type="submit" value="Add Notice">
    </form>
</div>

</body>
</html>
