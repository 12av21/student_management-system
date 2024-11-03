<?php  
session_start();
include '../config.php'; // Database connection

// Check if the connection to the database works
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch courses and semesters
$courses = $conn->query("SELECT course_id, course_name FROM courses");
$semesters = $conn->query("SELECT semester_id, semester_name FROM semesters");

$enrolled_students = [];
$subjects = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['course_id']) && isset($_POST['semester_id'])) {
        $course_id = $_POST['course_id'];
        $semester_id = $_POST['semester_id'];

        // Fetch enrolled students based on the selected course and semester
        $stmt = $conn->prepare("SELECT id, name FROM students WHERE course_id = ? AND semester_id = ?");
        if ($stmt) {
            $stmt->bind_param("ii", $course_id, $semester_id);
            $stmt->execute();
            $enrolled_students = $stmt->get_result();
        } else {
            echo "Error in student query: " . $conn->error;
        }

        // Fetch subjects for the selected semester
        $stmt = $conn->prepare("SELECT subject_id, subject_name FROM subjects WHERE semester_id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $semester_id);
            $stmt->execute();
            $subjects = $stmt->get_result();
        }

        if ($subjects->num_rows === 0) {
            $no_subjects_msg = "No subjects found for the selected semester.";
        }
        if ($enrolled_students->num_rows === 0) {
            $no_students_msg = "No students enrolled for the selected course and semester.";
        }
    }

    // Handle marks submission for students in multiple subjects
    if (isset($_POST['submit_marks'])) {
        foreach ($_POST['marks_obtained'] as $student_id => $subject_marks) {
            foreach ($subject_marks as $subject_id => $marks_obtained) {
                $total_marks = $_POST['total_marks'][$subject_id];

                // Insert or update marks for each student-subject pair
                $stmt = $conn->prepare("INSERT INTO results (student_id, subject_id, marks_obtained, total_marks) VALUES (?, ?, ?, ?) 
                                        ON DUPLICATE KEY UPDATE marks_obtained = VALUES(marks_obtained), total_marks = VALUES(total_marks)");
                $stmt->bind_param("iiii", $student_id, $subject_id, $marks_obtained, $total_marks);
                $stmt->execute();
            }
        }

        $success = "Marks submitted successfully!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Marks</title>
    <link rel="stylesheet" href="manage_marks.css">

</head>
<body>
<div class="container">
    <h1>Manage Student Marks</h1>

    <?php if (isset($success)): ?>
        <div class="alert success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" action="" class="selection-form">
        <label for="course_id">Course</label>
        <select name="course_id" required>
            <option value="">Select Course</option>
            <?php while ($course = $courses->fetch_assoc()): ?>
                <option value="<?= $course['course_id'] ?>" <?= isset($_POST['course_id']) && $_POST['course_id'] == $course['course_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($course['course_name']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="semester_id">Semester</label>
        <select name="semester_id" required>
            <option value="">Select Semester</option>
            <?php while ($semester = $semesters->fetch_assoc()): ?>
                <option value="<?= $semester['semester_id'] ?>" <?= isset($_POST['semester_id']) && $_POST['semester_id'] == $semester['semester_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($semester['semester_name']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <button type="submit">Show Enrolled Students & Subjects</button>
        <a href="admin_dashboard.php" class="back-button">Back to Dashboard</a>
    </form>

    <?php if (isset($no_subjects_msg)): ?>
        <div class="alert"><?= $no_subjects_msg ?></div>
    <?php endif; ?>
    <?php if (isset($no_students_msg)): ?>
        <div class="alert"><?= $no_students_msg ?></div>
    <?php endif; ?>

    <?php if ($enrolled_students && $subjects && $enrolled_students->num_rows > 0 && $subjects->num_rows > 0): ?>
        <form method="POST" action="" class="marks-form">
            <input type="hidden" name="course_id" value="<?= $_POST['course_id'] ?>">
            <input type="hidden" name="semester_id" value="<?= $_POST['semester_id'] ?>">

            <table>
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <?php while ($subject = $subjects->fetch_assoc()): ?>
                            <th>
                                <?= htmlspecialchars($subject['subject_name']) ?><br>
                                <input type="number" name="total_marks[<?= $subject['subject_id'] ?>]" 
                                       placeholder="Total Marks" min="0" required>
                            </th>
                        <?php endwhile; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $enrolled_students->data_seek(0); // Reset students pointer
                    while ($student = $enrolled_students->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($student['name']) ?></td>
                            <?php 
                            $subjects->data_seek(0); // Reset subjects pointer for each student
                            while ($subject = $subjects->fetch_assoc()): ?>
                                <td>
                                    <input type="number" name="marks_obtained[<?= $student['id'] ?>][<?= $subject['subject_id'] ?>]" 
                                           placeholder="Marks Obtained" min="0" required>
                                </td>
                            <?php endwhile; ?>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <button type="submit" name="submit_marks">Submit Marks</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
