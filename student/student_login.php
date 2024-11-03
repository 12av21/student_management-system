<!---student_login.php---->
<?php 
session_start();
include '../config.php';
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetch student details
    $query = "SELECT * FROM students WHERE email = '$email'";
    $result = mysqli_query($conn, $query);
    $student = mysqli_fetch_assoc($result);

    // Check if student exists and password matches
    if ($student && password_verify($password, $student['password'])) {
        $_SESSION['student_id'] = $student['id'];
        
        // Check if password has been changed from default
        if ($student['password_changed'] == 0) {
            header("Location: change_password.php"); // Redirect to change password page
            exit();
        } else {
            header("Location: student_dashboard.php"); // Redirect to main dashboard
            exit();
        }
    } else {
        echo "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login</title>
    <link rel="stylesheet" href="student_login.css">

</head>
<body>
    <div class="form-container">
        <h2>Student Login</h2>
        <?php if ($message): ?>
            <div class="alert"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <!-- Login Form -->
        <form method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" name="login" class="btn-submit">Login</button>
        </form>

        <div class="extra-links">
            <p>Don't have an account? <a href="registration.php">Register here</a>.</p>
            <a href="../index.html">Back to Main page</a>
        </div>
    </div>
</body>
</html>
