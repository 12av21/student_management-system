<?php
session_start();
include '../config.php'; // Database connection

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle course deletion
if (isset($_GET['delete_course_id'])) {
    $course_id = $_GET['delete_course_id'];

    // Delete the course from the database
    $stmt = $conn->prepare("DELETE FROM courses WHERE course_id = ?");
    $stmt->bind_param("i", $course_id);

    if ($stmt->execute()) {
        $success = "Course deleted successfully.";
    } else {
        $error = "Error deleting course: " . $stmt->error;
    }
}

// Fetch all courses with course_fee
$query = "SELECT course_id, course_name, course_duration, course_fee FROM courses";
$courses = $conn->query($query);

// Error handling for database query
if (!$courses) {
    die("Error fetching courses: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Courses</title>
    <link rel="stylesheet" href="manage_courses.css">
</head>
<body>
    <div class="container mt-5">
        <div class="card shadow p-4">
            <h1 class="text-center text-primary mb-4">Manage Courses</h1>

            <!-- Display success or error message -->
            <?php if (isset($success)): ?>
                <div class="alert alert-success text-center"><?php echo $success; ?></div>
            <?php elseif (isset($error)): ?>
                <div class="alert alert-danger text-center"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Button to Add a New Course -->
            <div class="d-flex justify-content-end mb-3">
                <a href="add_courses.php" class="btn btn-success">+ Add New Course</a>
            </div>

            <h2 class="text-secondary">Courses</h2>
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">Course Name</th>
                            <th scope="col">Course Duration (Years)</th>
                            <th scope="col">Course Fee (₹)</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Display courses -->
                        <?php while ($course = $courses->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                                <td><?php echo htmlspecialchars($course['course_duration']); ?></td>
                                <td>₹<?php echo htmlspecialchars(number_format($course['course_fee'], 2)); ?></td>
                                <td>
                                    <a href="edit_course.php?course_id=<?php echo $course['course_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="manage_subjects.php?course_id=<?php echo $course['course_id']; ?>" class="btn btn-info btn-sm">Manage Subjects</a>
                                    <a href="?delete_course_id=<?php echo $course['course_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this course?');">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-3 text-center">
                <a href="admin_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
            </div>
        </div>
    </div>
</body>
</html>
