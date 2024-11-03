<?php   
session_start();
include '../config.php'; // Database connection

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $course_name = $_POST['course_name'];
    $course_duration = $_POST['course_duration'];
    $course_fee = $_POST['course_fee'];

    // Insert the course into the database, including course fee
    $stmt = $conn->prepare("INSERT INTO courses (course_name, course_duration, course_fee) VALUES (?, ?, ?)");
    $stmt->bind_param("sii", $course_name, $course_duration, $course_fee); // 'i' for integer fee and duration

    if ($stmt->execute()) {
        // Redirect to manage courses page after successful insertion
        header("Location: manage_courses.php");
    } else {
        $error = "Error adding course: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Course</title>
    <link rel="stylesheet" href="add_course.css"> <!-- Link to external CSS -->
</head>
<body>
    <div class="container">
        <h1>Add New Course</h1>

        <!-- Display Error Message if Any -->
        <?php if (isset($error)): ?>
            <div class="error-message">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <!-- Add Course Form -->
        <form action="" method="POST">
            <div class="form-group">
                <label for="course_name">Course Name:</label>
                <input type="text" name="course_name" required>
            </div>
            <div class="form-group">
                <label for="course_duration">Course Duration (Years):</label>
                <input type="number" name="course_duration" required>
            </div>
            <div class="form-group">
                <label for="course_fee">Course Fee (₹):</label>
                <input type="number" name="course_fee" step="0.01" required>
            </div>
            
            <button type="submit" class="submit-btn">Add Course</button>
            <a href="manage_courses.php" class="back-btn">Back to Courses</a>
        </form>
    </div>
</body>
</html>
