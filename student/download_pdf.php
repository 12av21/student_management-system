<?php
session_start();
include '../config.php';

// Check if student ID is set in the session
if (!isset($_SESSION['student_id'])) {
    die("Student ID not found. Please log in.");
}

$student_id = $_SESSION['student_id'];

// Prepare and execute query to fetch student's results with subject names
$stmt = $conn->prepare("
    SELECT s.subject_name, r.marks_obtained, r.total_marks 
    FROM results r 
    JOIN subjects s ON r.subject_id = s.subject_id 
    WHERE r.student_id = ?
");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$results = $stmt->get_result();
$stmt->close();
$conn->close();

// Prepare the content in plain text format
$output = "Student Results\n\n";
$output .= "Subject\tMarks Obtained\tTotal Marks\tPercentage\n";
$output .= "----------------------------------------------\n";

// Loop through results and add to output
while ($row = $results->fetch_assoc()) {
    $subject = htmlspecialchars($row['subject_name']);
    $marks_obtained = htmlspecialchars($row['marks_obtained']);
    $total_marks = htmlspecialchars($row['total_marks']);
    $percentage = round(($marks_obtained / $total_marks) * 100, 2) . '%';

    $output .= "$subject\t$marks_obtained\t$total_marks\t$percentage\n";
}

// Set headers for download
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="Student_Results.pdf"');
header('Content-Length: ' . strlen($output));

// Output the content for download
echo $output;
exit();
?>
