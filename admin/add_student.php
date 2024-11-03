<?php  
session_start();
include '../config.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php';

// Check if user is admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle form submission
if (isset($_POST['add_student'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $father_name = $_POST['father_name'];
    $address = $_POST['address'];
    $course_id = $_POST['course_id']; // Get course ID from dropdown
    $semester_id = $_POST['semester_id']; // Get semester ID from dropdown
    $contact = $_POST['contact'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $enrollment = $_POST['enrollment'];
    $enrollment_date = $_POST['enrollment_date'];

    // Generate random password
    $password = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()"), 0, 8);
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert student data
    $query = "INSERT INTO students (name, email, father_name, address, course_id, semester_id, enrollment, enrollment_date, contact, dob, gender, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssssssssss", $name, $email, $father_name, $address, $course_id, $semester_id, $enrollment, $enrollment_date, $contact, $dob, $gender, $hashed_password);

    if ($stmt->execute()) {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = ''; // Change to your email
            $mail->Password = ''; // Change to your email password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('adarshverma7376@gmail.com', 'OTP Service');
            $mail->addAddress($email);  

            $mail->isHTML(true); 
            $mail->Subject = 'Student Login Credentials';
            $mail->Body = "Dear $name,<br><br>Your account has been created. Here are your login details:<br><br>Username: $email<br>Password: $password<br><br>Please log in and change your password.";

            $mail->send();
            echo 'Student added and email sent!';
        } catch (Exception $e) {
            echo "Student added, but email could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "Failed to add student.";
    }
}

// Fetch available courses
$courses_query = "SELECT * FROM courses"; 
$courses_result = $conn->query($courses_query);

// Fetch available semesters
$semesters_query = "SELECT * FROM semesters"; 
$semesters_result = $conn->query($semesters_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student</title>
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
        <h1>Add New Student</h1>
        <form method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="father_name" class="form-label">Father's Name</label>
                    <input type="text" id="father_name" name="father_name" class="form-control" required>
                </div>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <input type="text" id="address" name="address" class="form-control" required>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="course_id" class="form-label">Course</label>
                    <select id="course_id" name="course_id" class="form-control" required>
                        <option value="">Select Course</option>
                        <?php while ($course = $courses_result->fetch_assoc()): ?>
                            <option value="<?= $course['course_id']; ?>"><?= htmlspecialchars($course['course_name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="semester_id" class="form-label">Semester</label>
                    <select id="semester_id" name="semester_id" class="form-control" required>
                        <option value="">Select Semester</option>
                        <?php while ($semester = $semesters_result->fetch_assoc()): ?>
                            <option value="<?= $semester['semester_id']; ?>"><?= htmlspecialchars($semester['semester_id']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label for="enrollment" class="form-label">Enrollment</label>
                <input type="text" id="enrollment" name="enrollment" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="enrollment_date" class="form-label">Enrollment Date</label>
                <input type="date" id="enrollment_date" name="enrollment_date" class="form-control">
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="contact" class="form-label">Contact</label>
                    <input type="text" id="contact" name="contact" class="form-control" required>
                </div>
            </div>
            <div class="mb-3">
                <label for="dob" class="form-label">Date of Birth</label>
                <input type="date" id="dob" name="dob" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="gender" class="form-label">Gender</label>
                <select id="gender" name="gender" class="form-control" required>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="d-flex justify-content-between">
                <button type="submit" name="add_student" class="btn btn-primary">Add Student</button>
                <a href="admin_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
            </div>
        </form>
    </div>
</body>
</html>
