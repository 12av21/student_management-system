<?php 
session_start();
include '../config.php'; // Database connection

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch all courses for the dropdown
$courses = $conn->query("SELECT * FROM courses");
if (!$courses) {
    die("Query failed: " . $conn->error); // Debugging line
}

// Fetch semesters (assuming 8 semesters)
$semesters = range(1, 8);

// Initialize attendance records and total attendance summary
$attendance_records = [];
$total_attendance_summary = [];
$chart_data = []; // Data for the pie chart

// Fetch students based on course and semester
$students = null; // Initially set to null
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['course_id'], $_POST['semester'])) {
    $course_id = (int)$_POST['course_id'];
    $semester = (int)$_POST['semester'];

    // Get students for the selected course and semester
    $stmt = $conn->prepare("SELECT id, name FROM students WHERE course_id = ? AND semester_id = ?");
    $stmt->bind_param("ii", $course_id, $semester);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result) {
        $students = $result;
    } else {
        die("Query failed: " . $stmt->error); // Debugging line
    }
    
    // Fetch attendance records if date is set
    if (!empty($_POST['date'])) {
        $date = $_POST['date'];
        
        $stmt = $conn->prepare("SELECT student_id, status FROM attendance WHERE course_id = ? AND semester = ? AND date = ?");
        $stmt->bind_param("iis", $course_id, $semester, $date);
        $stmt->execute();
        $attendance_records = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $attendance_records = array_column($attendance_records, 'status', 'student_id');
    }

    // Fetch total attendance summary for each student
    $summary_stmt = $conn->prepare("SELECT student_id, status, COUNT(*) as count 
                                    FROM attendance 
                                    WHERE course_id = ? AND semester = ? 
                                    GROUP BY student_id, status");
    $summary_stmt->bind_param("ii", $course_id, $semester);
    $summary_stmt->execute();
    $summary_result = $summary_stmt->get_result();

    // Organize summary data by student_id
    while ($row = $summary_result->fetch_assoc()) {
        $student_id = $row['student_id'];
        $status = $row['status'];
        $count = $row['count'];

        if (!isset($total_attendance_summary[$student_id])) {
            $total_attendance_summary[$student_id] = ['Present' => 0, 'Absent' => 0, 'Late' => 0, 'Excused' => 0];
        }
        
        $total_attendance_summary[$student_id][$status] = $count;
    }

    // Prepare chart data
    foreach ($total_attendance_summary as $student_id => $attendance) {
        $chart_data[$student_id] = [
            'Present' => $attendance['Present'],
            'Absent' => $attendance['Absent'],
            'Late' => $attendance['Late'],
            'Excused' => $attendance['Excused'],
        ];
    }
}

// Handle attendance submission
if (isset($_POST['submit_attendance']) && !empty($_POST['date'])) {
    $date = $_POST['date'];
    
    foreach ($_POST['attendance'] as $student_id => $status) {
        $course_id = $_POST['course_id'];
        $semester = $_POST['semester'];
        
        // Insert or update attendance record for each student
        $stmt = $conn->prepare("INSERT INTO attendance (student_id, date, status, course_id, semester) 
                                VALUES (?, ?, ?, ?, ?)
                                ON DUPLICATE KEY UPDATE status = VALUES(status)");
        $stmt->bind_param("issii", $student_id, $date, $status, $course_id, $semester);
        $stmt->execute();
    }
    
    $success = "Attendance submitted successfully!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Attendance</title>
    <link rel="stylesheet" href="manage_attendance.css"> <!-- Custom CSS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Chart.js library -->
</head>
<body>
<div class="container">
    <a href="admin_dashboard.php" class="back-button">← Back to Dashboard</a>
    <h1>Manage Attendance</h1>

    <?php if (isset($success)): ?>
        <div class="alert success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="post" action="">
        <div class="row">
            <div class="col">
                <label for="course_id">Course</label>
                <select name="course_id" id="course_id" required>
                    <option value="">Select Course</option>
                    <?php while ($course = $courses->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($course['course_id']) ?>">
                            <?= htmlspecialchars($course['course_name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col">
                <label for="semester">Semester</label>
                <select name="semester" id="semester" required>
                    <option value="">Select Semester</option>
                    <?php foreach ($semesters as $sem): ?>
                        <option value="<?= htmlspecialchars($sem) ?>"><?= htmlspecialchars($sem) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col">
                <label for="date">Date</label>
                <input type="date" name="date" id="date" required>
            </div>
        </div>
        <div class="text-center">
            <button type="submit" class="btn">Show Students</button>
        </div>
    </form>

    <?php if ($students && $students instanceof mysqli_result && $students->num_rows > 0): ?>
        <form method="post" action="">
            <input type="hidden" name="course_id" value="<?= htmlspecialchars($_POST['course_id']) ?>">
            <input type="hidden" name="semester" value="<?= htmlspecialchars($_POST['semester']) ?>">
            <input type="hidden" name="date" value="<?= htmlspecialchars($_POST['date']) ?>">
            
            <table class="table">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Present</th>
                        <th>Absent</th>
                        <th>Late</th>
                        <th>Excused</th>
                        <th>Attendance Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($student = $students->fetch_assoc()): ?>
                        <?php
                            $student_id = $student['id'];
                            $present_count = $total_attendance_summary[$student_id]['Present'] ?? 0;
                            $absent_count = $total_attendance_summary[$student_id]['Absent'] ?? 0;
                            $late_count = $total_attendance_summary[$student_id]['Late'] ?? 0;
                            $excused_count = $total_attendance_summary[$student_id]['Excused'] ?? 0;
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($student['name']) ?></td>
                            <td><?= htmlspecialchars($present_count) ?></td>
                            <td><?= htmlspecialchars($absent_count) ?></td>
                            <td><?= htmlspecialchars($late_count) ?></td>
                            <td><?= htmlspecialchars($excused_count) ?></td>
                            <td>
                                <div class="attendance-buttons">
                                    <input type="hidden" name="attendance[<?= htmlspecialchars($student['id']) ?>]" value="Absent">
                                    <button type="button" class="btn present" onclick="setAttendance(this, 'Present')">Present</button>
                                    <button type="button" class="btn late" onclick="setAttendance(this, 'Late')">Late</button>
                                    <button type="button" class="btn excused" onclick="setAttendance(this, 'Excused')">Excused</button>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            
            <div class="text-center">
                <button type="submit" name="submit_attendance" class="btn">Submit Attendance</button>
            </div>
        </form>

        <!-- Pie Chart Section -->
        <div class="chart-container mt-5">
            <h2>Attendance Status Summary</h2>
            <canvas id="attendanceChart"></canvas>
        </div>

        <script>
            const chartData = <?= json_encode($chart_data) ?>;

            const ctx = document.getElementById('attendanceChart').getContext('2d');
            const data = {
                labels: ['Present', 'Absent', 'Late', 'Excused'],
                datasets: Object.keys(chartData).map(student_id => ({
                    label: student_id,
                    data: [
                        chartData[student_id]['Present'] || 0,
                        chartData[student_id]['Absent'] || 0,
                        chartData[student_id]['Late'] || 0,
                        chartData[student_id]['Excused'] || 0
                    ],
                    backgroundColor: ['#28a745', '#dc3545', '#ffc107', '#17a2b8'],
                    borderColor: '#fff',
                    borderWidth: 1
                })),
            };

            const config = {
                type: 'pie',
                data: data,
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true,
                        }
                    }
                },
            };

            const attendanceChart = new Chart(ctx, config);

            function setAttendance(button, status) {
                const input = button.closest('tr').querySelector('input[type="hidden"]');
                input.value = status;
                button.parentElement.querySelectorAll('.btn').forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');
            }
        </script>
    <?php endif; ?>
</div>
</body>
</html>
