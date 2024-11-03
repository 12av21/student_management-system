<?php
session_start();
include '../config.php'; // Include the database connection

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Check if student ID is provided via GET
if (!isset($_GET['id'])) {
    header("Location: manage_students.php");
    exit();
}

$student_id = $_GET['id'];

// Fetch student details
$student_query = "SELECT s.id, s.name, s.email, s.father_name, s.address, s.contact, s.dob, s.gender, s.enrollment, s.enrollment_date, 
                  c.course_name, sm.semester_name 
                  FROM students s
                  JOIN courses c ON s.course_id = c.course_id
                  JOIN semesters sm ON s.semester_id = sm.semester_id
                  WHERE s.id = ?";
$stmt = $conn->prepare($student_query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$student_result = $stmt->get_result();

// Check if student exists
if ($student_result->num_rows === 0) {
    echo "Student not found!";
    exit();
}

$student = $student_result->fetch_assoc();
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Student Details</title>
    <link rel="stylesheet" href="view_student.css">
</head>
<body>
    <div class="wrapper">
        <aside class="sidebar">
            <h2>Admin Panel</h2>
            <nav>
                <ul>
                    <li><a href="manage_students.php">Manage Students</a></li>
                    <li><a href="view_attendance.php">View Attendance</a></li>
                    <li><a href="manage_courses.php">Manage Courses</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </aside>
        
        <main class="content">
            <h1>Student Details</h1>
            <div class="student-info">
                <table>
                    <tr>
                        <th>ID</th>
                        <td><?= htmlspecialchars($student['id']); ?></td>
                    </tr>
                    <tr>
                        <th>Name</th>
                        <td><?= htmlspecialchars($student['name']); ?></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><?= htmlspecialchars($student['email']); ?></td>
                    </tr>
                    <tr>
                        <th>Father's Name</th>
                        <td><?= htmlspecialchars($student['father_name']); ?></td>
                    </tr>
                    <tr>
                        <th>Course</th>
                        <td><?= htmlspecialchars($student['course_name']); ?></td>
                    </tr>
                    <tr>
                        <th>Semester</th>
                        <td><?= htmlspecialchars($student['semester_name']); ?></td>
                    </tr>
                    <tr>
                        <th>Enrollment Number</th>
                        <td><?= htmlspecialchars($student['enrollment']); ?></td>
                    </tr>
                    <tr>
                        <th>Contact</th>
                        <td><?= htmlspecialchars($student['contact']); ?></td>
                    </tr>
                    <tr>
                        <th>Date of Birth</th>
                        <td><?= htmlspecialchars($student['dob']); ?></td>
                    </tr>
                    <tr>
                        <th>Enrollment Date</th>
                        <td><?= htmlspecialchars($student['enrollment_date']); ?></td>
                    </tr>
                </table>
            </div>
            <a href="manage_students.php" class="btn">Back to Manage Students</a>
        </main>
    </div>
</body>
</html>
