<?php  
session_start();
include '../config.php';

// Ensure the student is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: student_login.php");
    exit();
}

// Fetch the semesters, courses, and fee details for the logged-in student
$student_id = $_SESSION['student_id'];
$query = "
    SELECT semesters.semester_name, courses.course_name, fees.amount 
    FROM enrollment
    JOIN semesters ON enrollment.semester_id = semesters.semester_id
    JOIN courses ON enrollment.course_id = courses.course_id
    JOIN fees ON courses.course_id = fees.course_id AND enrollment.semester_id = fees.semester_id
    WHERE enrollment.student_id = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    echo "SQL Error: " . $conn->error; // Debugging
    exit();
}

$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

$course_details = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $course_details[] = $row;
    }
} 

// Debugging output
echo "<pre>";
print_r($course_details); // Check what is returned
echo "</pre>";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Course, Semester, and Fees | Student Management System</title>
    <link rel="stylesheet" href="fee_submission.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4; /* Light background */
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            margin-bottom: 20px;
            color: #007bff;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            text-align: center;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .no-data {
            text-align: center;
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Your Course, Semester, and Fees Details</h1>
        <table>
            <thead>
                <tr>
                    <th>Semester Name</th>
                    <th>Course Name</th>
                    <th>Fee Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($course_details)): ?>
                    <?php foreach ($course_details as $entry): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($entry['semester_name']); ?></td>
                            <td><?php echo htmlspecialchars($entry['course_name']); ?></td>
                            <td><?php echo htmlspecialchars($entry['amount']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="no-data">No course, semester, or fee details found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <a href="student_dashboard.php" class="btn">Back to Dashboard</a>
    </div>
</body>
</html>
