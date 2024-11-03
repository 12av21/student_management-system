<?php
session_start();
include '../config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Get the subject ID from the URL
if (!isset($_GET['subject_id'])) {
    die("Subject ID not provided.");
}

$subject_id = $_GET['subject_id'];

// Prepare and execute the delete statement
$stmt = $conn->prepare("DELETE FROM subjects WHERE subject_id = ?");
$stmt->bind_param("i", $subject_id);

if ($stmt->execute()) {
    $success = "Subject deleted successfully.";
} else {
    $error = "Error deleting subject: " . $stmt->error;
}

// Redirect back to the manage subjects page after deletion
$course_id = $_GET['course_id'] ?? null; // Optional: Capture course_id if needed for redirection
$stmt->close();
$conn->close();

header("Location: manage_subjects.php?course_id=" . $course_id);
exit();
