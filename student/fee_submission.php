<?php
session_start();
include '../config.php'; // Database connection

// Check if student is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: student_login.php");
    exit();
}

// Get the student's course ID
$student_id = $_SESSION['student_id'];
$stmt = $conn->prepare("SELECT c.course_fee FROM students s 
                        JOIN courses c ON s.course_id = c.course_id 
                        WHERE s.id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$stmt->bind_result($course_fee);
$stmt->fetch();
$stmt->close();

// Handle fee payment (simplified example)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $payment_method = $_POST['payment_method'];
    // Process payment logic here based on selected method
    $success = "Fee payment successful. Amount Paid: ₹" . number_format($course_fee, 2) . " using " . htmlspecialchars($payment_method);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Fee</title>
    <link rel="stylesheet" href="fee_submission.css"> <!-- Link to external CSS -->
</head>
<body>
    <div class="container">
        <h1>Fee Submission</h1>

        <?php if (isset($success)): ?>
            <div class="success-message">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="course_fee">Course Fee (₹):</label>
                <input type="text" id="course_fee" value="<?php echo htmlspecialchars(number_format($course_fee, 2)); ?>" readonly>
            </div>

            <div class="form-group">
                <label for="payment_method">Select Payment Method:</label>
                <select name="payment_method" id="payment_method" required>
                    <option value="">--Select--</option>
                    <option value="UPI">UPI</option>
                    <option value="Debit/Credit Card">Debit/Credit Card</option>
                    <option value="Internet Banking">Internet Banking</option>
                </select>
            </div>

            <button type="submit" class="submit-btn">Pay Fee</button>
            <a href="student_dashboard.php" class="back-btn">Back to Dashboard</a>
        </form>
    </div>
</body>
</html>
