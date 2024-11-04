<?php 
session_start();
include '../config.php';

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch available courses and semesters
$courses = $conn->query("SELECT course_id, course_name FROM courses");
$semesters = $conn->query("SELECT semester_id, semester_name FROM semesters");

// Process form submission for filtering students
$enrolled_students = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id = $_POST['course_id'] ?? null;
    $semester_id = $_POST['semester_id'] ?? null;
    
    if ($course_id && $semester_id) {
        $stmt = $conn->prepare("SELECT id, name, email, enrollment_date FROM students WHERE course_id = ? AND semester_id = ?");
        $stmt->bind_param("ii", $course_id, $semester_id);
        $stmt->execute();
        $enrolled_students = $stmt->get_result();
    } else {
        echo "<p>Please select both course and semester.</p>";
    }
} else {
    // Fetch all students if no filter is applied
    $students_query = "SELECT id, name, email, enrollment_date FROM students ORDER BY id DESC";
    $enrolled_students = $conn->query($students_query);
}

// Delete student if requested (with CSRF protection)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete_id'], $_SESSION['csrf_token'], $_GET['token']) && $_SESSION['csrf_token'] === $_GET['token']) {
    $delete_id = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    header("Location: manage_students.php");
    exit();
}

// Generate CSRF token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
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
        
        <div class="filter-form">
            <section>
                <form class="filter-form" action="" method="POST">
                    <div class="form-group">
                        <label for="course_id">Select Course:</label>
                        <select id="course_id" name="course_id" required>
                            <option value="">Choose Course</option>
                            <?php while ($course = $courses->fetch_assoc()): ?>
                                <option value="<?= htmlspecialchars($course['course_id']) ?>">
                                    <?= htmlspecialchars($course['course_name']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="semester_id">Select Semester:</label>
                        <select id="semester_id" name="semester_id" required>
                            <option value="">Choose Semester</option>
                            <?php while ($semester = $semesters->fetch_assoc()): ?>
                                <option value="<?= htmlspecialchars($semester['semester_id']) ?>">
                                    <?= htmlspecialchars($semester['semester_name']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn">Filter Students</button>
                </form>
            </section>
        </div>

        <button class="back-button" onclick="window.history.back();">Back</button>
        <button class="add-student-button" onclick="window.location.href='add_student.php';">Add New Student</button>

        <section>
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
                    <?php if ($enrolled_students->num_rows > 0): ?>
                        <?php while ($row = $enrolled_students->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['id']) ?></td>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td><?= htmlspecialchars($row['enrollment_date']) ?></td>
                                <td class="actions">
                                <a href="view_student.php?id=<?= htmlspecialchars($row['id']) ?>" class="btn view-btn">View</a>
                                <a href="edit_students.php?id=<?= htmlspecialchars($row['id']) ?>" class="btn edit-btn">Edit</a>
                                <a href="manage_students.php?delete_id=<?= htmlspecialchars($row['id']) ?>&token=<?= $_SESSION['csrf_token'] ?>" class="btn delete-btn" onclick="return confirm('Are you sure you want to delete this student?');">Delete</a>
                                </td>

                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5">No students found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </div>

</body>
</html>
