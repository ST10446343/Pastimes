<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<?php
include "DBConn.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullName = trim($_POST["fullName"]);
    $email = trim($_POST["email"]);
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    $confirmPassword = trim($_POST["confirmPassword"]);
    $deliveryAddress = trim($_POST["deliveryAddress"]);

    if ($password !== $confirmPassword) {
        $message = "Passwords do not match.";
    } elseif (strlen($password) != 8) {
        $message = "Password must be exactly 8 characters.";
    } else {
        $passwordHash = md5($password);

        $sql = "INSERT INTO tblUser 
                (fullName, email, username, passwordHash, deliveryAddress, userStatus)
                VALUES (?, ?, ?, ?, ?, 'Pending')";

        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssss", $fullName, $email, $username, $passwordHash, $deliveryAddress);

        if (mysqli_stmt_execute($stmt)) {
            $message = "Registration successful. Please wait for admin verification.";
        } else {
            $message = "Registration failed. Username or email may already exist.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - Pastimes</title>
</head>
<body>

<h2>User Registration</h2>

<p style="color:red;"><?php echo $message; ?></p>

<form method="POST">
    <label>Full Name:</label><br>
    <input type="text" name="fullName" required><br><br>

    <label>Email:</label><br>
    <input type="email" name="email" required><br><br>

    <label>Username:</label><br>
    <input type="text" name="username" required><br><br>

    <label>Password:</label><br>
    <input type="password" name="password" minlength="8" maxlength="8" required><br><br>

    <label>Confirm Password:</label><br>
    <input type="password" name="confirmPassword" minlength="8" maxlength="8" required><br><br>

    <label>Delivery Address:</label><br>
    <input type="text" name="deliveryAddress" required><br><br>

    <button type="submit">Register</button>
</form>

<br>
<a href="login.php">Already registered? Login here</a>

</body>
</html>