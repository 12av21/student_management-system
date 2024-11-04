<?php 
session_start();
include '../config.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php'; // PHPMailer autoload

$message = '';
$otpMessage = '';

// Check if the user is starting a new registration attempt
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Clear session if a new registration attempt is made
    if (isset($_SESSION['otp_generated'])) {
        unset($_SESSION['otp']);
        unset($_SESSION['email']);
        unset($_SESSION['otp_generated']);
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Generate or Resend OTP
    if (isset($_POST['generate_otp']) || isset($_POST['resend_otp'])) {
        $email = $_POST['email'];

        // Check if email is already registered
        $query = "SELECT * FROM students WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0 && !isset($_POST['resend_otp'])) {
            // If email is already registered and not resending
            $message = "This email is already registered. Please log in instead.";
        } else {
            // Generate OTP if the email is not already registered or resend OTP
            $otp = rand(100000, 999999);
            $_SESSION['otp'] = $otp;
            $_SESSION['email'] = $email;  // Store email in session for later use
            $_SESSION['otp_generated'] = true;  // Mark that OTP is generated

            // Send OTP via email using PHPMailer
            $mail = new PHPMailer(true);

            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';    // SMTP server
                $mail->SMTPAuth   = true;
                $mail->Username   = ''; // Your email
                $mail->Password   = '';    // SMTP password or app password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                // Recipients
                $mail->setFrom('adarshverma7376@gmail.com', 'OTP Service');
                $mail->addAddress($email);  // User email

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Your OTP Code';
                $mail->Body    = "Your OTP code is: <b>$otp</b>";

                $mail->send();
                $otpMessage = "OTP sent! Please check your email.";
            } catch (Exception $e) {
                $otpMessage = "Error: Could not send OTP. Mailer Error: {$mail->ErrorInfo}.";
            }
        }
    }

    if (isset($_POST['register'])) {
        $name = $_POST['name'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $otp = $_POST['otp'];

        // Validate OTP
        if ($otp != $_SESSION['otp']) {
            $otpMessage = "Invalid OTP!";
        } else if ($password != $confirm_password) {
            $message = "Passwords do not match!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert user into the database
            $query = "INSERT INTO students (name, email, password) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sss", $name, $_SESSION['email'], $hashed_password);
            
            if ($stmt->execute()) {
                // Optionally set session variables for login
                $_SESSION['student_id'] = $stmt->insert_id; // Get the last inserted ID
                $_SESSION['name'] = $name; // Save name to session
                unset($_SESSION['otp']); // Clear OTP after successful registration
                unset($_SESSION['email']); // Clear email
                unset($_SESSION['otp_generated']); // Clear OTP generation status
                
                // Redirect to login page or dashboard directly
                header("Location: student_dashboard.php"); // Redirect to dashboard after successful login
                exit();
            } else {
                $message = "Error: Could not register.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration with OTP</title>
    <link rel="stylesheet" href="registration.css">
</head>
<body>
    <div class="form-container">
        <h2>Student Registration</h2>
        <?php if ($message): ?>
            <div class="alert"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if ($otpMessage): ?>
            <div class="alert alert-info"><?php echo $otpMessage; ?></div>
        <?php endif; ?>

        <!-- Registration Form -->
        <form method="POST">
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" name="email" required class="form-control" value="<?php echo isset($_SESSION['email']) ? $_SESSION['email'] : ''; ?>" <?php echo isset($_SESSION['otp_generated']) ? 'readonly' : ''; ?>>

            <?php if (!isset($_SESSION['otp_generated'])): ?>
                <!-- OTP Generation Button -->
                <button type="submit" name="generate_otp" class="btn">Generate OTP</button>
            <?php else: ?>
                <label for="name" class="form-label">Name</label>
                <input type="text" id="name" name="name" required class="form-control">

                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" required class="form-control">

                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required class="form-control">

                <label for="otp" class="form-label">OTP</label>
                <input type="text" id="otp" name="otp" required class="form-control">

                <!-- Register Button -->
                <button type="submit" name="register" class="btn">Register</button>
                
                <!-- Resend OTP Button -->
                <button type="submit" name="resend_otp" class="btn" style="background-color: #e67e22;">Resend OTP</button>

                <a href="student_login.php" class="btn" style="background-color: #6c757d;">Back to Dashboard</a>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>
