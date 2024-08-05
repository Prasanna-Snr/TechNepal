<?php
session_start();
include 'databaseConn.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $inputPassword = htmlspecialchars($_POST['psw']);
    $email = $_SESSION['email'];
    $passwordCheckSql = "SELECT psw FROM users WHERE email='$email'";
    $queryResult = $databaseConnection->query($passwordCheckSql);

    if ($queryResult->num_rows > 0) {
        $user = $queryResult->fetch_assoc();
        $hashedPassword = $user['psw'];
        if (password_verify($inputPassword, $hashedPassword)) {
            header("Location: dashboard.php");
            exit();
        } else {
            echo "<script>alert('Incorrect password. Please try again.');</script>";
        }
    } else {
        echo "<script>alert('User not found.');</script>";
    }

    $databaseConnection->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Verification</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #00c6ff, #0072ff);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }
        .container {
            background-color: #ffffff;
            border-radius: 0.5rem;
            box-shadow: 0 0 1rem rgba(0, 0, 0, 0.1);
            padding: 2rem;
            width: 100%;
            max-width: 500px;
        }
        .form-control {
            font-size: 0.875rem;
        }
        .form-control::placeholder {
            font-size: 0.875rem;
        }
        .btn-primary {
            background-color: #28a745;
            border: none;
        }
        .btn-primary:hover {
            background-color: #218838;
        }
        label {
            color: #0072ff;
        }
        h1 {
            color: #0072ff;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center">Password Verification</h1>
        <form action="pswPage.php" method="post">
            <div class="form-group">
                <label for="psw">Enter Password:</label>
                <input type="password" id="psw" name="psw" class="form-control" placeholder="Password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Submit</button>
        </form>
    </div>
</body>
</html>
