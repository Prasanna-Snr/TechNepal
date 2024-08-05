 <?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'databaseConn.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php'; 

// Function to create a 6-character OTP
function createOtp() {
    $otpCharacters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $oneTimePassword = '';
    for ($i = 0; $i < 6; $i++) {
        $oneTimePassword .= $otpCharacters[random_int(0, strlen($otpCharacters) - 1)];
    }
    return $oneTimePassword;
}

// Function to dispatch OTP via email
function dispatchOtpEmail($email, $oneTimePassword) {
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; 
        $mail->SMTPAuth = true;
        $mail->Username = 'prasannasunuwar03@gmail.com'; 
        $mail->Password = 'qnsz peby oylh vvlq'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('TechNepal03@gmail.com', 'TechNepal'); 
        $mail->addAddress($email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'OTP Code';
        $mail->Body    = 'Your OTP code is: <bold>' . $oneTimePassword . '</bold><br>' .
                         'This OTP is valid for 2 minutes only. Please use it within this time frame.';

        $mail->send();
        return true;
    } catch (Exception $e) {
        // Handle errors
        echo "<script>alert('Failed to send OTP: " . $mail->ErrorInfo . "');</script>";
        return false;
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['resend'])) {
        // Resend OTP functionality
        if (isset($_SESSION['email'])) {
            $email = $_SESSION['email'];
            $newOtp = createOtp();
            $_SESSION['otp'] = $newOtp;
            $_SESSION['otp_timestamp'] = time();
            if (dispatchOtpEmail($email, $newOtp)) {
                echo "<script>alert('New OTP has been sent to your email.');</script>";
            }
        } else {
            echo "<script>alert('Unable to resend OTP. Please request a new one.');</script>";
        }
    } elseif (isset($_POST['verify'])) {
        // Verify OTP functionality
        $inputOtp = htmlspecialchars($_POST['otp']);
        if (isset($_SESSION['otp']) && isset($_SESSION['otp_timestamp'])) {
            $storedOtp = $_SESSION['otp'];
            $otpTimestamp = $_SESSION['otp_timestamp'];
            if (time() - $otpTimestamp <= 120) {
                if ($inputOtp === $storedOtp) {
                    header("Location: pswpage.php");
                    exit();
                } else {
                    echo "<script>alert('Incorrect OTP. Please try again.');</script>";
                }
            } else {
                echo "<script>alert('OTP has expired. Please request a new one.');</script>";
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
    <title>OTP Verification</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #00c6ff, #0072ff);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            background-color: #ffffff;
            border-radius: 0.5rem;
            box-shadow: 0 0 1rem rgba(0, 0, 0, 0.1);
            padding: 2rem;
            width: 100%;
            max-width: 500px;
        }
        .btn-primary {
            background-color: #28a745;
            border: none;
        }
        .btn-primary:hover {
            background-color: #218838;
        }
        .btn-secondary {
            background-color: #6c757d;
            border: none;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        .form-control {
            font-size: 0.875rem;
        }
        .form-control::placeholder {
            font-size: 0.875rem;
        }
        h1 {
            color: #0072ff;
            margin-bottom: 2rem;
        }
        label {
            color: #0072ff;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center">OTP Verification</h1>
        <form action="otpPage.php" method="post">
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

