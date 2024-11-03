<?php 
session_start();
include '../config.php';

// Ensure the student is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: student_login.php");
    exit();
}

// Fetch student details from the database
$student_id = $_SESSION['student_id'];

// Modified query to join students and courses
$query = "SELECT students.*, courses.course_name FROM students 
          LEFT JOIN courses ON students.course_id = courses.course_id 
          WHERE students.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $student = $result->fetch_assoc();
} else {
    session_destroy();
    header("Location: student_login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard | Student Management System</title>
    <link rel="stylesheet" href="student_dashboard.css">
</head>
<body>

<div class="sidebar">
    <h2>Student Dashboard</h2>
    <a href="#">Home</a>
    <a href="view_profile.php">View Profile</a>
    <a href="student_view_attendance.php">View Attendance</a>
    <a href="edit_student.php">Edit Details</a>
    <a href="results.php">Result</a>
    <a href="fee_submission.php">Fee details</a>


   
    
    <a href="../logout.php">Logout</a>
</div>

<div class="content">
    <h1>Welcome, <?php echo htmlspecialchars($student['name']); ?>!</h1>

    

    <!-- Cards -->
    <div class="card">
        <h3>View Profile</h3>
        <p>See your personal details</p>
        <a href="view_profile.php">View Profile</a>
    </div>

    <div class="card">
        <h3>View Attendance</h3>
        <p>Check your attendance records</p>
        <a href="student_view_attendance.php">View Attendance</a>
    </div>

</div>

<div class="footer">
    &copy; 2024 Student Management System
</div>

</body>
</html>
