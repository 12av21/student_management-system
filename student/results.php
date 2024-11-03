<?php 
session_start();
include '../config.php';

// Check if student ID is set in the session
if (!isset($_SESSION['student_id'])) {
    die("Student ID not found. Please log in.");
}

$student_id = $_SESSION['student_id']; // Retrieve student ID from session

// Prepare and execute query to fetch student's results with subject names
$stmt = $conn->prepare("
    SELECT s.subject_name, r.marks_obtained, r.total_marks 
    FROM results r 
    JOIN subjects s ON r.subject_id = s.subject_id 
    WHERE r.student_id = ?
");

if ($stmt === false) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param("i", $student_id);
$stmt->execute();
$results = $stmt->get_result();

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Results</title>
    <style>
        body { font-family: Arial, sans-serif; }
        h1 { text-align: center; }
        table { width: 80%; margin: 20px auto; border-collapse: collapse; }
        table, th, td { border: 1px solid #ddd; }
        th, td { padding: 12px; text-align: center; }
        th { background-color: #f2f2f2; }
        .download-button {
            display: block;
            width: fit-content;
            margin: 20px auto;
            text-align: center;
        }
    </style>
</head>
<body>
    <h1>Your Results</h1>
    <?php if ($results && $results->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Subject</th>
                    <th>Marks Obtained</th>
                    <th>Total Marks</th>
                    <th>Percentage</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $results->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['subject_name']) ?></td>
                        <td><?= htmlspecialchars($row['marks_obtained']) ?></td>
                        <td><?= htmlspecialchars($row['total_marks']) ?></td>
                        <td><?= round(($row['marks_obtained'] / $row['total_marks']) * 100, 2) ?>%</td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <form action="download_pdf.php" method="post" class="download-button">
            <button type="submit">Download PDF</button>
        </form>
    <?php else: ?>
        <p style="text-align: center;">No results available.</p>
    <?php endif; ?>
</body>
</html>
