<?php
include 'databaseConn.php'; 

$emailExists = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fname = htmlspecialchars($_POST['fname']);
    $mname = htmlspecialchars($_POST['mname']);
    $lname = htmlspecialchars($_POST['lname']);
    $dob = htmlspecialchars($_POST['dob']);
    $gender = htmlspecialchars($_POST['gender']);
    $email = htmlspecialchars($_POST['email']);
    $psw = htmlspecialchars($_POST['psw']);

    // Check if email already exists
    $emailCheckSql = "SELECT * FROM users WHERE email='$email'";
    $queryResult = $databaseConnection->query($emailCheckSql);

    if ($queryResult->num_rows > 0) {
        $emailExists = true;
    } else {
        // Hash the password for security
        $passwordHash = password_hash($psw, PASSWORD_DEFAULT);

        // Insert data into the database
        $insertSql = "INSERT INTO users (fname, mname, lname, dob, gender, email, psw) VALUES ('$fname', '$mname', '$lname', '$dob', '$gender', '$email', '$passwordHash')";

        if ($databaseConnection->query($insertSql) === TRUE) {
            // Redirect to login page on successful registration
            header("Location: loginPage.php");
            exit();
        } else {
            $databaseErrorMessage = "Error: " . $insertSql . "<br>" . $databaseConnection->error;
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
    <title>Registration Page</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #00c6ff, #0072ff);
        }
        .container {
            background-color: #f8f9fa;
            border-radius: 0.5rem;
            box-shadow: 0 0 1rem rgba(0, 0, 0, 0.1);
            padding: 2rem;
            max-width: 800px;
            margin: auto;
            margin-top: 5rem;
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
        a {
            color: #28a745;
        }
        a:hover {
            color: #1e7e34;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center mb-4">Register</h1>
        <form action="registrationPage.php" method="post">
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="fname">First Name:</label>
                    <input type="text" id="fname" name="fname" class="form-control form-control-lg" placeholder="First Name" required>
                </div>
                <div class="form-group col-md-4">
                    <label for="mname">Middle Name:</label>
                    <input type="text" id="mname" name="mname" class="form-control form-control-lg" placeholder="Middle Name">
                </div>
                <div class="form-group col-md-4">
                    <label for="lname">Last Name:</label>
                    <input type="text" id="lname" name="lname" class="form-control form-control-lg" placeholder="Last Name" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="dob">Date of Birth:</label>
                    <input type="date" id="dob" name="dob" class="form-control form-control-lg" required>
                </div>
                <div class="form-group col-md-6">
                    <label>Gender:</label><br>
                    <div class="form-check form-check-inline">
                        <input type="radio" id="male" name="gender" value="male" class="form-check-input">
                        <label for="male" class="form-check-label">Male</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="radio" id="female" name="gender" value="female" class="form-check-input">
                        <label for="female" class="form-check-label">Female</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="radio" id="other" name="gender" value="other" class="form-check-input">
                        <label for="other" class="form-check-label">Other</label>
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" class="form-control form-control-lg" placeholder="Email" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="psw">Password:</label>
                    <input type="password" id="psw" name="psw" class="form-control form-control-lg" placeholder="Password" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-block btn-lg">Register</button>
        </form>
        <p class="text-center mt-3">Have an account? <a href="loginPage.php">Login</a></p>
    </div>
    <?php
    if ($emailExists) {
        echo "<script>alert('Email already exists.');</script>";
    }
    ?>
</body>
</html>
