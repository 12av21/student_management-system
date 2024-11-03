<?php  
session_start();
include '../config.php';

// Check if user is admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Check if the student ID is set in the URL
if (isset($_GET['id'])) {
    $student_id = $_GET['id'];

    // Fetch student data
    $query = "SELECT s.id, s.name, s.email, s.father_name, s.address, s.contact, s.dob, s.gender, s.enrollment, s.enrollment_date, s.course_id, c.course_name, s.semester_id, sm.semester_id AS semester_id, sm.semester_name
              FROM students s
              JOIN courses c ON s.course_id = c.course_id
              JOIN semesters sm ON s.semester_id = sm.semester_id
              WHERE s.id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $student_result = $stmt->get_result();

    // Check if student exists
    if ($student_result->num_rows === 0) {
        echo "Student not found!";
        exit();
    }

    $student = $student_result->fetch_assoc();
} else {
    echo "Invalid student ID!";
    exit();
}

// Handle form submission
if (isset($_POST['update_student'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $father_name = $_POST['father_name'];
    $address = $_POST['address'];
    $course_id = $_POST['course_id'];
    $semester_id = $_POST['semester_id'];
    $contact = $_POST['contact'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $enrollment = $_POST['enrollment'];
    $enrollment_date = $_POST['enrollment_date'];

    // Update student data
    $update_query = "UPDATE students SET name = ?, email = ?, father_name = ?, address = ?, course_id = ?, semester_id = ?, enrollment = ?, enrollment_date = ?, contact = ?, dob = ?, gender = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("ssssssssssi", $name, $email, $father_name, $address, $course_id, $semester_id, $enrollment, $enrollment_date, $contact, $dob, $gender, $student_id);

    if ($update_stmt->execute()) {
        echo "Student details updated successfully!";
        // Optionally redirect or show a message
    } else {
        echo "Failed to update student details.";
    }
}

// Fetch courses for the dropdown
$courses_query = "SELECT * FROM courses";
$courses_result = $conn->query($courses_query);

// Fetch semesters for the dropdown
$semesters_query = "SELECT * FROM semesters";
$semesters_result = $conn->query($semesters_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f7f9;
        }
        .container {
            max-width: 700px;
            margin-top: 50px;
        }
        h1 {
            font-size: 2.5rem;
            margin-bottom: 30px;
            text-align: center;
            color: #007bff;
        }
        .form-control {
            border-radius: 8px;
        }
        .form-label {
            font-weight: bold;
        }
        button {
            border-radius: 8px;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container bg-white p-5 shadow-sm rounded">
        <h1>Edit Student Details</h1>
        <form method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($student['name']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="father_name" class="form-label">Father's Name</label>
                    <input type="text" id="father_name" name="father_name" class="form-control" value="<?= htmlspecialchars($student['father_name']); ?>" required>
                </div>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <input type="text" id="address" name="address" class="form-control" value="<?= htmlspecialchars($student['address']); ?>" required>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="course_id" class="form-label">Course</label>
                    <select id="course_id" name="course_id" class="form-control" required>
                        <option value="">Select Course</option>
                        <?php while ($course = $courses_result->fetch_assoc()): ?>
                            <option value="<?= $course['id']; ?>" <?= ($course['id'] == $student['course_id']) ? 'selected' : ''; ?>><?= htmlspecialchars($course['course_name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="semester_id" class="form-label">Semester</label>
                    <select id="semester_id" name="semester_id" class="form-control" required>
                        <option value="">Select Semester</option>
                        <?php while ($semester = $semesters_result->fetch_assoc()): ?>
                            <option value="<?= $semester['id']; ?>" <?= ($semester['id'] == $student['semester_id']) ? 'selected' : ''; ?>><?= htmlspecialchars($semester['semester']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label for="enrollment" class="form-label">Enrollment</label>
                <input type="text" id="enrollment" name="enrollment" class="form-control" value="<?= htmlspecialchars($student['enrollment']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="enrollment_date" class="form-label">Enrollment Date</label>
                <input type="date" id="enrollment_date" name="enrollment_date" class="form-control" value="<?= htmlspecialchars($student['enrollment_date']); ?>">
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($student['email']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="contact" class="form-label">Contact</label>
                    <input type="text" id="contact" name="contact" class="form-control" value="<?= htmlspecialchars($student['contact']); ?>" required>
                </div>
            </div>
            <div class="mb-3">
                <label for="dob" class="form-label">Date of Birth</label>
                <input type="date" id="dob" name="dob" class="form-control" value="<?= htmlspecialchars($student['dob']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="gender" class="form-label">Gender</label>
                <select id="gender" name="gender" class="form-control" required>
                    <option value="male" <?= ($student['gender'] == 'male') ? 'selected' : ''; ?>>Male</option>
                    <option value="female" <?= ($student['gender'] == 'female') ? 'selected' : ''; ?>>Female</option>
                    <option value="other" <?= ($student['gender'] == 'other') ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>
            <div class="d-flex justify-content-between">
                <button type="submit" name="update_student" class="btn btn-primary">Update Student</button>
                <a href="admin_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
            </div>
        </form>
    </div>
</body>
</html>
