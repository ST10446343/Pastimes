<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include "DBConn.php";

$message = "";
$username = "";
$email = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    $passwordHash = md5($password);

    $sql = "SELECT * FROM tblUser 
            WHERE username = ? AND email = ?";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $username, $email);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);

        if ($user["userStatus"] != "Verified") {
            $message = "Your account is still pending admin verification.";
        } elseif ($user["passwordHash"] == $passwordHash) {
            $_SESSION["userID"] = $user["userID"];
            $_SESSION["fullName"] = $user["fullName"];

            $message = "User " . $user["fullName"] . " is logged in.";
        } else {
            $message = "Incorrect password. Please try again.";
        }
    } else {
        $message = "User does not exist. Please register first.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Pastimes</title>
</head>
<body>

<h2>User Login</h2>

<p style="color:red;"><?php echo $message; ?></p>

<form method="POST">
    <label>Username:</label><br>
    <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" required><br><br>

    <label>Email:</label><br>
    <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required><br><br>

    <label>Password:</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">Login</button>
</form>

<br>
<a href="register.php">Register here</a>
<br>
<a href="adminLogin.php">Admin Login</a>

</body>
</html>