<?php   
session_start();
include '../config.php';

// Ensure the student is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: student_login.php");
    exit();
}

// Fetch attendance records for the student
$student_id = $_SESSION['student_id'];
$query = "SELECT * FROM attendance WHERE student_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

$attendance_records = [];
while ($row = $result->fetch_assoc()) {
    $attendance_records[] = $row;
}

// Calculate attendance statistics
$total_classes = count($attendance_records);
$present = 0;
$absent = 0;

foreach ($attendance_records as $record) {
    if ($record['status'] === 'Present') {
        $present++;
    } else {
        $absent++;
    }
}

$attendance_data = [
    'present' => $present,
    'absent' => $absent
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Attendance | Student Management System</title>
    <link rel="stylesheet" href="attendance.css"> <!-- Link to external CSS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <h1>Attendance Records</h1>
        <div class="chart-container">
            <canvas id="attendanceChart" width="10" height="10"></canvas> <!-- Smaller canvas size -->
        </div>
        <script>
            const ctx = document.getElementById('attendanceChart').getContext('2d');
            const attendanceData = <?php echo json_encode($attendance_data); ?>;

            const attendanceChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Present', 'Absent'],
                    datasets: [{
                        data: [attendanceData.present, attendanceData.absent],
                        backgroundColor: ['#36a2eb', '#ff6384'],
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true, // Maintain aspect ratio to fit the small size
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                    }
                }
            });
        </script>

        <?php if (empty($attendance_records)): ?>
            <div class="alert alert-warning">No attendance records found for this student.</div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($attendance_records as $record): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($record['date']); ?></td>
                            <td><?php echo htmlspecialchars($record['status']); ?></td>
                            <td><?php echo htmlspecialchars($record['remarks']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        <a href="student_dashboard.php" class="btn">Back</a>
    </div>
</body>
</html>
