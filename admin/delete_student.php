<!---delete_student.php--->

<?php
session_start();
include '../config.php'; // Database connection

$id = $_GET['id'];

// Delete student from the database
$query = "DELETE FROM students WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: manage_students.php");
?>
