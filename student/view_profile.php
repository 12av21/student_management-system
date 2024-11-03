<?php  
session_start();
include '../config.php';

// Ensure the student is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: student_login.php");
    exit();
}

// Fetch student details from the database with course name
$student_id = $_SESSION['student_id'];
$query = "SELECT students.*, courses.course_name 
          FROM students 
          LEFT JOIN courses ON students.course_id = courses.course_id 
          WHERE students.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $student = $result->fetch_assoc();
} else {
    // If the student is not found, redirect to login
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
    <title>View Profile | Student Management System</title>
    <link rel="stylesheet" href="view_profile.css"> <!-- Link to external CSS file -->
</head>
<body>

<div class="container">
    <div class="profile-header">
        <h1>Profile</h1>
        <img src="uploads/<?php echo htmlspecialchars($student['profile_picture']); ?>" alt="Profile Picture">
    </div>

    <table>
        <tr>
            <th>ID</th>
            <td><?php echo htmlspecialchars($student['id']); ?></td>
        </tr>
        <tr>
            <th>Name</th>
            <td><?php echo htmlspecialchars($student['name']); ?></td>
        </tr>
        <tr>
            <th>Father's Name</th>
            <td><?php echo htmlspecialchars($student['father_name']); ?></td>
        </tr>
        <tr>
            <th>Date of Birth</th>
            <td><?php echo htmlspecialchars($student['dob']); ?></td>
        </tr>
        <tr>
            <th>Enrollment</th>
            <td><?php echo htmlspecialchars($student['enrollment']); ?></td>
        </tr>
        <tr>
            <th>Course</th>
            <td><?php echo htmlspecialchars($student['course_name']); ?></td>
        </tr>
        <tr>
            <th>Semester</th>
            <td><?php echo htmlspecialchars($student['semester_id']); ?></td>
        </tr>
        <tr>
            <th>Address</th>
            <td><?php echo htmlspecialchars($student['address']); ?></td>
        </tr>
        <tr>
            <th>Email</th>
            <td><?php echo htmlspecialchars($student['email']); ?></td>
        </tr>
        <tr>
            <th>Contact</th>
            <td><?php echo htmlspecialchars($student['contact']); ?></td>
        </tr>
        <tr>
            <th>Gender</th>
            <td><?php echo htmlspecialchars($student['gender']); ?></td>
        </tr>
    </table>

    <div class="button-group">
    <a href="upload_profile.php" class="btn btn-primary">Edit Profile picture</a>

        <a href="edit_student.php" class="btn btn-primary">Edit Profile</a>
        <a href="../logout.php" class="btn btn-danger">Logout</a>
        <a href="student_dashboard.php" class="btn btn-secondary">Back</a>
    </div>
</div>

</body>
</html>
