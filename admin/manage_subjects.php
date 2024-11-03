<?php
session_start();
include '../config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Get the course_id from the URL
if (!isset($_GET['course_id'])) {
    die("Course ID not provided.");
}

$course_id = $_GET['course_id'];

// Fetch course information for display
$course_query = $conn->prepare("SELECT course_name FROM courses WHERE course_id = ?");
$course_query->bind_param("i", $course_id);
$course_query->execute();
$course_result = $course_query->get_result();
$course = $course_result->fetch_assoc();

// Fetch subjects specific to this course
$subjects_query = $conn->prepare("SELECT subject_id, subject_name FROM subjects WHERE course_id = ?");
$subjects_query->bind_param("i", $course_id);
$subjects_query->execute();
$subjects = $subjects_query->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Subjects for <?php echo htmlspecialchars($course['course_name']); ?></title>
    <link rel="stylesheet" href="manage_courses.css">
</head>
<body>
    <div class="container mt-5">
        <div class="card shadow p-4">
            <h1 class="text-center text-primary mb-4">Manage Subjects for <?php echo htmlspecialchars($course['course_name']); ?></h1>

            <div class="d-flex justify-content-end mb-3">
                <a href="add_subject.php?course_id=<?php echo $course_id; ?>" class="btn btn-success">+ Add New Subject</a>
            </div>

            <h2 class="text-secondary">Subjects</h2>
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">Subject Name</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($subject = $subjects->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($subject['subject_name']); ?></td>
                                <td>
                                    <a href="edit_subject.php?subject_id=<?php echo $subject['subject_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="delete_subject.php?subject_id=<?php echo $subject['subject_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this subject?');">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-3 text-center">
                <a href="manage_courses.php" class="btn btn-secondary">Back to Courses</a>
            </div>
        </div>
    </div>
</body>
</html>
