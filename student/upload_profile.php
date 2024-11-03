<?php
session_start();
include '../config.php';

// Ensure the student is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: student_login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Handle profile picture upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_picture'])) {
    $file = $_FILES['profile_picture'];

    // Validate file type and size (only JPEG, PNG, GIF; max size 2MB)
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    if (in_array($file['type'], $allowed_types) && $file['size'] < 4000000) { // 2MB limit
        $upload_dir = 'uploads/'; // Directory for uploaded files
        $file_name = time() . '_' . basename($file['name']); // Create a unique file name
        $target_file = $upload_dir . $file_name;

        // Move file to upload directory
        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            // Update profile picture path in database
            $query = "UPDATE students SET profile_picture = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("si", $file_name, $student_id);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                $_SESSION['profile_picture'] = $file_name; // Update session variable
                // Redirect to the home page
                header("Location: student_dashboard.php"); // Update with your actual dashboard page
                exit();
            } else {
                echo "<script>alert('Failed to update profile picture.');</script>";
            }
        } else {
            echo "<script>alert('Error uploading file.');</script>";
        }
    } else {
        echo "<script>alert('Invalid file type or file is too large.');</script>";
    }
}

// Fetch the current profile picture from the database
$query = "SELECT profile_picture FROM students WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$current_profile_picture = $student['profile_picture'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Profile Picture</title>
    <style>
        /* General Styles */
        body {
            font-family: 'Arial', sans-serif;
            background: url('path/to/your/background-image.jpg') no-repeat center center fixed; /* Background image */
            background-size: cover; /* Cover the whole page */
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh; /* Full viewport height */
        }

        .container {
            width: 400px; /* Fixed width for the container */
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.9); /* Semi-transparent background */
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        h1 {
            color: #34495e;
        }

        .profile-picture {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
        }

        input[type="file"] {
            margin: 10px 0;
            padding: 5px;
        }

        button {
            background-color: #3498db;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem; /* Larger font size for button */
            transition: background-color 0.3s; /* Smooth transition */
        }

        button:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Upload Profile Picture</h1>

        <!-- Display Current Profile Picture -->
        <img src="uploads/<?= $current_profile_picture ?>" alt="Profile Picture" class="profile-picture">
        <p>Change your profile picture:</p>

        <!-- Upload Form -->
        <form action="" method="POST" enctype="multipart/form-data">
            <input type="file" name="profile_picture" accept="image/*" required>
            <br>
            <button type="submit">Upload</button>
        </form>
    </div>
</body>
</html>
