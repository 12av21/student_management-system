<?php
session_start();
include '../config.php'; // Include the database connection

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle notice submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['notice'])) {
    $notice = $_POST['notice'];
    $stmt = $conn->prepare("INSERT INTO notices (text) VALUES (?)");
    $stmt->bind_param("s", $notice);
    $stmt->execute();
    $success = "Notice added successfully!";
}

// Fetch all notices
$notices_query = "SELECT * FROM notices ORDER BY id DESC";
$notices_result = $conn->query($notices_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Notices</title>
    <link rel="stylesheet" href="manage_notices.css">
</head>
<body>
<nav>
    <h1>Manage Notices</h1>
    <div>
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="logout.php">Logout</a>
    </div>
</nav>

<div class="dashboard-container">
   

    <div class="admin-profile">
        <h2>Notice Board</h2>
        <form method="post" action="">
            <textarea name="notice" required placeholder="Write your notice here..." rows="4"></textarea>
            <button type="submit" class="btn">Add Notice</button>
        </form>
        <?php if (isset($success)): ?>
            <div class="alert success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Notice</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($notice = $notices_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $notice['id']; ?></td>
                    <td><?php echo $notice['text']; ?></td>
                    <td>
                        <a href="edit_notice.php?id=<?php echo $notice['id']; ?>">Edit</a>
                        <a href="delete_notice.php?id=<?php echo $notice['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<footer>
    &copy; 2024 Student Management System
</footer>
</body>
</html>
