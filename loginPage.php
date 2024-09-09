<?php
session_start();
include 'databaseConn.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php'; 

function createOtp() {
    return substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 6);
}

function dispatchOtpEmail($email, $otp) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'prasannasunuwar03@gmail.com';
        $mail->Password = 'qnsz peby oylh vvlq';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->setFrom('TechNepal03@gmail.com', 'TechNepal');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'OTP Code';
        $mail->Body    = "Your OTP code is: <b>$otp</b><br>This OTP is valid for 2 minutes only.";
        $mail->send();
        return true;
    } catch (Exception $e) {
        echo "<script>alert('Failed to send OTP: {$mail->ErrorInfo}');</script>";
        return false;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = htmlspecialchars($_POST['email']);
    $emailExists = $databaseConnection->query("SELECT 1 FROM users WHERE email='$email'")->num_rows > 0;

    if ($emailExists) {
        $otp = createOtp();
        if (dispatchOtpEmail($email, $otp)) {
            $_SESSION['otp'] = $otp;
            $_SESSION['otp_timestamp'] = time();
            $_SESSION['email'] = $email;
            header("Location: otpPage.php");
            exit();
        } else {
            echo "<script>alert('Failed to send OTP. Please try again.');</script>";
        }
    } else {
        echo "<script>alert('Email does not exist. Please sign up.');</script>";
    }

    $databaseConnection->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(to right, #00c6ff, #0072ff); height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card { background-color: #f8f9fa; border-radius: 0.5rem; box-shadow: 0 0 1rem rgba(0, 0, 0, 0.1); padding: 2rem; width: 100%; max-width: 500px; }
        .form-control { font-size: 0.875rem; }
        .form-control::placeholder { font-size: 0.875rem; }
        .btn-primary { background-color: #28a745; border: none; }
        .btn-primary:hover { background-color: #218838; }
        a { color: #28a745; }
        a:hover { color: #1e7e34; }
        .card-title { color: #0072ff; }
    </style>
</head>
<body>
    <div class="card shadow-sm">
        <h1 class="card-title text-center mb-4">Login</h1>
        <form method="post">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" class="form-control form-control-lg" placeholder="Email" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block btn-lg">Login</button>
        </form>
        <p class="text-center mt-3">Don’t have an account? <a href="registrationPage.php">Sign up</a></p>
    </div>
</body>
</html>
