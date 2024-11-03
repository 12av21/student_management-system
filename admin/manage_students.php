<?php
session_start();
include '../config.php'; // Include the database connection

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch all students from the database
$students_query = "SELECT id, name, email, enrollment_date FROM students ORDER BY id DESC";
$students_result = $conn->query($students_query);


// Delete student if requested
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_query = "DELETE FROM students WHERE id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    header("Location: manage_students.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students</title>
    <link rel="stylesheet" href="manage_students.css">
</head>
<body>

<div class="container">
    <h1>Manage Students</h1>
    <a href="admin_dashboard.php" class="add-student-btn">Back to Home</a>
    <a href="add_student.php" class="add-student-btn">Add New Student</a>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Enrollment Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $students_result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['email']; ?></td>
                <td><?php echo $row['enrollment_date']; ?></td>
                <td class="actions">
                    <a href="view_student.php?id=<?php echo $row['id'];?>">View</a>
                    <a href="edit_students.php?id=<?php echo $row['id']; ?>">Edit</a>
                    <a href="manage_students.php?delete_id=<?php echo $row['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this student?');">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
