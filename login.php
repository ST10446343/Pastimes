<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include "DBConn.php";

$message = "";
$username = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    $passwordHash = md5($password);

    $sql = "SELECT * FROM tblUser WHERE username = ?";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);

        if ($user["userStatus"] != "Verified") {
            $message = "<p class='error'>Your account is still pending admin verification.</p>";
        } elseif ($user["passwordHash"] == $passwordHash) {
            $_SESSION["userID"] = $user["userID"];
            $_SESSION["fullName"] = $user["fullName"];

            header("Location: dashboard.php");
            exit();
        } else {
            $message = "<p class='error'>Incorrect password. Please try again.</p>";
        }
    } else {
        $message = "<p class='error'>User does not exist. Please register first.</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Pastimes</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="container">
    <h2>Login</h2>

    <?= $message ?>

    <form method="POST">
        <input type="text" name="username" placeholder="Username" required
               value="<?= htmlspecialchars($username) ?>">

        <input type="password" name="password" placeholder="Password" required>

        <button type="submit">Login</button>
    </form>

    <a href="register.php">Create account</a>
</div>

</body>
</html>