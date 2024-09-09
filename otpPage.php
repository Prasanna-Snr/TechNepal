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
        $mail->Body = "Your OTP code is: <b>$otp</b><br>Valid for 2 minutes.";
        $mail->send();
        return true;
    } catch (Exception $e) {
        echo "<script>alert('Failed to send OTP: {$mail->ErrorInfo}');</script>";
        return false;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_SESSION['email'] ?? null;
    if (isset($_POST['resend']) && $email) {
        $newOtp = createOtp();
        $_SESSION['otp'] = $newOtp;
        $_SESSION['otp_timestamp'] = time();
        if (dispatchOtpEmail($email, $newOtp)) {
            echo "<script>alert('New OTP sent.');</script>";
        }
    } elseif (isset($_POST['verify'])) {
        $inputOtp = htmlspecialchars($_POST['otp']);
        if (isset($_SESSION['otp'], $_SESSION['otp_timestamp']) && time() - $_SESSION['otp_timestamp'] <= 120) {
            if ($inputOtp === $_SESSION['otp']) {
                header("Location: pswpage.php");
                exit();
            } else {
                echo "<script>alert('Incorrect OTP.');</script>";
            }
        } else {
            echo "<script>alert('OTP expired.');</script>";
        }
    }
    $databaseConnection->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(to right, #00c6ff, #0072ff); height: 100vh; display: flex; align-items: center; justify-content: center; }
        .container { background-color: #ffffff; border-radius: 0.5rem; box-shadow: 0 0 1rem rgba(0, 0, 0, 0.1); padding: 2rem; max-width: 500px; }
        .form-control { font-size: 0.875rem; }
        .form-control::placeholder { font-size: 0.875rem; }
        .btn-primary { background-color: #28a745; border: none; }
        .btn-primary:hover { background-color: #218838; }
        .btn-secondary { background-color: #6c757d; border: none; }
        .btn-secondary:hover { background-color: #5a6268; }
        h1, label { color: #0072ff; }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center">OTP Verification</h1>
        <form method="post">
            <div class="form-group">
                <label for="otp">Enter OTP:</label>
                <input type="text" id="otp" name="otp" class="form-control form-control-lg" placeholder="Enter OTP">
            </div>
            <button type="submit" name="verify" class="btn btn-primary btn-block btn-lg">Verify OTP</button>
            <button type="submit" name="resend" class="btn btn-secondary btn-block btn-lg mt-2">Resend OTP</button>
        </form>
    </div>
</body>
</html>
