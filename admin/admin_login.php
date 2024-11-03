<?php 
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../config.php'; // Include database connection

// Check if the admin is already logged in
if (isset($_SESSION['admin_id'])) {
    header("Location: admin_dashboard.php");
    exit();
}

$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Admin credentials (hardcoded for now)
    $admin_username = "admin"; // Replace with your actual admin username
    $admin_password = "password"; // Replace with your actual admin password

    // Validate login credentials
    if ($username === $admin_username && $password === $admin_password) {
        $_SESSION['admin_id'] = 1; // Set the session variable (You can replace it with the actual admin ID)
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $message = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="admin_login.css">
</head>
<body>
<div class="login-container">
    <div class="login-icon">🔐</div> <!-- Icon can be a lock or other admin-related symbol -->
    <h2>Admin Login</h2>

    <?php if ($message): ?>
        <div class="error-message"><?php echo $message; ?></div>
    <?php endif; ?>

    <!-- Corrected form tag with method attribute -->
    <form method="POST" action="">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <button type="submit" class="login-button">Login</button>
    </form>
</div>
</body>
</html>
