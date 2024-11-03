<?php 
session_start();
include '../config.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: student_login.php"); // Redirect if not logged in
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password === $confirm_password) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $student_id = $_SESSION['student_id'];

        // Update password and mark as changed
        $query = "UPDATE students SET password = '$hashed_password', password_changed = 1 WHERE id = '$student_id'";
        mysqli_query($conn, $query);

        header("Location: student_dashboard.php"); // Redirect to main dashboard
        exit();
    } else {
        echo "<div class='alert'>Passwords do not match.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pure/2.0.6/pure-min.css">
    <style>
        /* General Page Styling */
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-image: url('../images/background.jpg'); /* Background Image */
            background-size: cover;
            background-position: center;
        }

        /* Centered Container for the Change Password Form */
        .form-container {
            background: rgba(255, 255, 255, 0.9);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: center;
        }

        h2 {
            font-size: 2rem;
            color: #2c3e50;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        label {
            display: block;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        input[type="password"]:focus {
            border-color: #3498db;
            outline: none;
        }

        .btn-submit {
            width: 100%;
            padding: 12px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s;
        }

        .btn-submit:hover {
            background-color: #2980b9;
        }

        .alert {
            margin: 10px 0;
            color: #e74c3c;
            background-color: #f8d7da;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Change Password</h2>
    <form method="POST" action="change_password.php">
        <div class="form-group">
            <label for="new_password">New Password</label>
            <input type="password" name="new_password" required>
        </div>
        <div class="form-group">
            <label for="confirm_password">Confirm New Password</label>
            <input type="password" name="confirm_password" required>
        </div>
        <button type="submit" class="btn-submit">Update Password</button>
    </form>
</div>

</body>
</html>
