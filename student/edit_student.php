<?php 
session_start();
include '../config.php';

// Ensure the student is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: student_login.php");
    exit();
}

// Fetch student details from session
$student_id = $_SESSION['student_id'];
$query = "SELECT * FROM students WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

// Fetch available semesters
$semesters_query = "SELECT * FROM semesters";
$semesters_result = $conn->query($semesters_query);

// Fetch available courses
$courses_query = "SELECT * FROM courses";
$courses_result = $conn->query($courses_query);

// Handle student update operation
$message = '';  
if (isset($_POST['update_student'])) {
    $name = $_POST['name'];
    $father_name = $_POST['father_name'];
    $address = $_POST['address'];
    $email = $_POST['email'];
    $dob = $_POST['dob'];
    $contact = $_POST['contact'];
    $course_id = $_POST['course_id'];
    $enrollment = $_POST['enrollment'];
    $gender = $_POST['gender'];
    $semester_id = $_POST['semester_id'];

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
    } else {
        // Update student's details
        $query = "UPDATE students SET name=?, father_name=?, address=?, email=?, dob=?, contact=?, course_id=?, enrollment=?, gender=?, semester_id=? WHERE id=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssssssssi", $name, $father_name, $address, $email, $dob, $contact, $course_id, $enrollment, $gender, $semester_id, $student_id);

        if ($stmt->execute()) {
            $message = "Details updated successfully!";
        } else {
            $message = "Failed to update details.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Your Details</title>
    <link rel="stylesheet" href="edit_student.css"> <!-- Link to external CSS file -->
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h1 class="mb-4">Edit Your Details</h1>
        
        <?php if ($message): ?>
            <div class="alert alert-info"><?= $message ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($student['name']) ?>" class="form-control" required>
            </div>
            
            <div class="mb-3">
                <label for="father_name" class="form-label">Father's Name</label>
                <input type="text" id="father_name" name="father_name" value="<?= htmlspecialchars($student['father_name']) ?>" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <input type="text" id="address" name="address" value="<?= htmlspecialchars($student['address']) ?>" class="form-control" required>
            </div>
            
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($student['email']) ?>" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="dob" class="form-label">Date of Birth</label>
                <input type="date" id="dob" name="dob" value="<?= htmlspecialchars($student['dob']) ?>" class="form-control" required>
            </div>
            
            <div class="mb-3">
                <label for="contact" class="form-label">Contact</label>
                <input type="text" id="contact" name="contact" value="<?= htmlspecialchars($student['contact']) ?>" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="course_id" class="form-label">Select Course</label>
                <select id="course_id" name="course_id" class="form-control" required>
                    <option value="">Choose Course</option>
                    <?php while ($course = $courses_result->fetch_assoc()): ?>
                        <option value="<?= $course['course_id']; ?>" <?= ($student['course_id'] == $course['course_id']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($course['course_name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="semester_id" class="form-label">Select Semester</label>
                <select id="semester_id" name="semester_id" class="form-control" required>
                    <option value="">Choose Semester</option>
                    <?php while ($semester = $semesters_result->fetch_assoc()): ?>
                        <option value="<?= $semester['semester_id']; ?>" <?= ($student['semester_id'] == $semester['semester_id']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($semester['semester_id']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="enrollment" class="form-label">Enrollment</label>
                <input type="text" id="enrollment" name="enrollment" value="<?= htmlspecialchars($student['enrollment']) ?>" class="form-control" required>
            </div>
   
            <div class="mb-3">
                <label for="gender" class="form-label">Gender</label>
                <input type="text" id="gender" name="gender" value="<?= htmlspecialchars($student['gender']) ?>" class="form-control" required>
            </div>

            <button type="submit" name="update_student" class="btn btn-primary">Update</button>
            <a href="student_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </form>
    </div>
</body>
</html>
