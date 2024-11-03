<?php 
session_start();
include '../config.php'; // Database connection

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Get course ID from the URL
$course_id = $_GET['course_id'];

// Fetch the current course details, including course fee
$stmt = $conn->prepare("SELECT course_name, course_duration, course_fee FROM courses WHERE course_id = ?");
$stmt->bind_param("i", $course_id);
$stmt->execute();
$stmt->bind_result($course_name, $course_duration, $course_fee);
$stmt->fetch();
$stmt->close();

// Handle form submission for updating course
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_course_name = $_POST['course_name'];
    $new_course_duration = $_POST['course_duration'];
    $new_course_fee = $_POST['course_fee'];

    // Update the course in the database
    $update_stmt = $conn->prepare("UPDATE courses SET course_name = ?, course_duration = ?, course_fee = ? WHERE course_id = ?");
    $update_stmt->bind_param("siii", $new_course_name, $new_course_duration, $new_course_fee, $course_id);

    if ($update_stmt->execute()) {
        // Redirect back to manage courses page after update
        header("Location: manage_courses.php");
    } else {
        $error = "Error updating course: " . $update_stmt->error;
    }

    $update_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Course</title>
    <link rel="stylesheet" href="edit_course.css">
</head>
<body>
    <div class="container">
        <h1>Edit Course</h1>

        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Edit Course Form -->
        <form action="" method="POST">
            <div class="form-group">
                <label for="course_name">Course Name:</label>
                <input type="text" name="course_name" value="<?php echo htmlspecialchars($course_name); ?>" required>
            </div>
            <div class="form-group">
                <label for="course_duration">Course Duration (Years):</label>
                <input type="number" name="course_duration" value="<?php echo htmlspecialchars($course_duration); ?>" required>
            </div>
            <div class="form-group">
                <label for="course_fee">Course Fee (₹):</label>
                <input type="number" name="course_fee" step="0.01" value="<?php echo htmlspecialchars($course_fee); ?>" required>
            </div>
            
            <button type="submit" class="submit-btn">Update Course</button>
            <a href="manage_courses.php" class="back-btn">Back to Courses</a>
        </form>
    </div>
</body>
</html>
