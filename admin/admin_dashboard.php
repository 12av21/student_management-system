<?php
session_start();
include '../config.php'; // Include the database connection

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch the latest admin details after update
$admin_id = $_SESSION['admin_id'];
$admin_query = "SELECT name, email, profile_pic FROM admins WHERE id = ?";
$stmt = $conn->prepare($admin_query);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$admin_result = $stmt->get_result();
$admin_data = $admin_result->fetch_assoc();

// Assign values to variables with fallbacks
$admin_name = !empty($admin_data['name']) ? htmlspecialchars($admin_data['name']) : 'Adarsh Verma'; // Sanitize output
$admin_email = !empty($admin_data['email']) ? htmlspecialchars($admin_data['email']) : 'adarshverma7376@gmail.com'; // Sanitize output
$admin_profile_pic = !empty($admin_data['profile_pic']) ? htmlspecialchars($admin_data['profile_pic']) : 'default_profile.png'; // Sanitize output

// Calculate totals using prepared statements
$courses_query = "SELECT COUNT(*) as total_courses FROM courses";
$courses_result = $conn->query($courses_query);
$total_courses = $courses_result->fetch_assoc()['total_courses'];

$students_query = "SELECT COUNT(*) as total_students FROM students";
$students_result = $conn->query($students_query);
$total_students = $students_result->fetch_assoc()['total_students'];

$notices_query = "SELECT COUNT(*) as total_notices FROM notices";
$notices_result = $conn->query($notices_query);
$total_notices = $notices_result->fetch_assoc()['total_notices'];

// Fetch the latest notices to display
$notices_fetch_query = "SELECT id, text FROM notices ORDER BY id DESC LIMIT 3";
$notices_result = $conn->query($notices_fetch_query);
$notices = $notices_result->fetch_all(MYSQLI_ASSOC); // Fetch all notices as an associative array

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin_dashboard.css">
</head>
<body>

<nav>
    <h1>Admin Dashboard</h1>
    <div>
        <a href="../logout.php" class="btn btn-primary">Logout</a>
    </div>
</nav>

<div class="dashboard-container">
    <div class="sidebar">
        <h3>Dashboard</h3>
        <a href="manage_students.php">Manage Students</a>
        <a href="manage_courses.php">Manage Courses</a>
        <a href="manage_marks.php">Manage Grades</a>
        <a href="manage_attendance.php">View Attendance</a>
        <a href="manage_notices.php">Notices</a>
        <a href="reports.php">Reports</a>
    </div>

    <div class="admin-profile">
        <img src="<?php echo $admin_profile_pic; ?>" alt="Admin Profile Picture">
        <h1><?php echo $admin_name; ?></h1>
        <h3><?php echo $admin_email; ?></h3>
        
        <div class="statistics">
            <div class="stat-box">Total Courses: <?php echo $total_courses; ?></div>
            <div class="stat-box">Total Students: <?php echo $total_students; ?></div>
            <div class="stat-box">Total Notices: <?php echo $total_notices; ?></div>
        </div>

        <div class="notices-section">
            <?php foreach ($notices as $notice): ?>
                <div class="notice-item" onclick="location.href='admin_dashboard.php?notice_id=<?php echo $notice['id']; ?>'">
                    <?php echo htmlspecialchars($notice['text']); ?> <!-- Sanitize output -->
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<footer>
    &copy; 2024 Student Management System
</footer>

</body>
</html>
