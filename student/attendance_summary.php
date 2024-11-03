<!--- Attendance summary --->


<?php 
session_start();
include '../config.php';

// Ensure the student is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: student_login.php");
    exit();
}

// Fetch attendance summary for the student
$student_id = $_SESSION['student_id'];
$query = "SELECT COUNT(*) AS total_days, 
                 SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) AS present_days,
                 SUM(CASE WHEN status = 'Absent' THEN 1 ELSE 0 END) AS absent_days
          FROM attendance WHERE student_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$summary = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Summary | Student Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Attendance Summary</h1>
        <table class="table table-bordered">
            <tr>
                <th>Total Days</th>
                <td><?php echo htmlspecialchars($summary['total_days']); ?></td>
            </tr>
            <tr>
                <th>Present Days</th>
                <td><?php echo htmlspecialchars($summary['present_days']); ?></td>
            </tr>
            <tr>
                <th>Absent Days</th>
                <td><?php echo htmlspecialchars($summary['absent_days']); ?></td>
            </tr>
        </table>
    </div>
</body>
</html>
