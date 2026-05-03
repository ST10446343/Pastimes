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
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="container">
    <h2>Register</h2>

    <?= $message ?>

    <form method="POST">
        <input type="text" name="fullName" placeholder="Full Name" required
               value="<?= $_POST['fullName'] ?? '' ?>">

        <input type="text" name="username" placeholder="Username" required
               value="<?= $_POST['username'] ?? '' ?>">

        <input type="email" name="email" placeholder="Email" required
               value="<?= $_POST['email'] ?? '' ?>">

        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="confirmPassword" placeholder="Confirm Password" required>
        <input type="text" name="deliveryAddress" placeholder="Delivery Address" required>

        <button type="submit">Register</button>
    </form>

    <a href="login.php">Already have an account?</a>
</div>

</body>
</html>