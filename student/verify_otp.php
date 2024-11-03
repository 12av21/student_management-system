<?php
session_start();

if (isset($_POST['verify_otp'])) {
    $user_otp = $_POST['otp'];

    // Check if OTP is stored in session
    if (isset($_SESSION['otp'])) {
        $stored_otp = $_SESSION['otp'];
        $otp_expiry = $_SESSION['otp_expiry'];

        // Check if OTP is correct and not expired
        if ($user_otp == $stored_otp && time() < $otp_expiry) {
            echo "OTP verified successfully!";
            // here we can proceed with whatever action comes next after OTP verification
        } else {
            echo "Invalid or expired OTP. Please try again.";
        }
    } else {
        echo "No OTP found. Please generate OTP first.";
    }
}
