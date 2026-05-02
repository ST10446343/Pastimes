<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include "DBConn.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $adminUsername = trim($_POST["adminUsername"]);
    $adminPassword = trim($_POST["adminPassword"]);

    $adminPasswordHash = md5($adminPassword);

    $sql = "SELECT * FROM tblAdmin 
            WHERE adminUsername = ? AND adminPasswordHash = ?";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $adminUsername, $adminPasswordHash);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 1) {
        $admin = mysqli_fetch_assoc($result);

        $_SESSION["adminID"] = $admin["adminID"];
        $_SESSION["adminName"] = $admin["adminName"];

        header("Location: verifyUsers.php");
        exit();
    } else {
        $message = "Invalid admin login details.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login - Pastimes</title>
</head>
<body>

<h2>Admin Login</h2>

<p style="color:red;"><?php echo $message; ?></p>

<form method="POST">
    <label>Admin Username:</label><br>
    <input type="text" name="adminUsername" required><br><br>

    <label>Admin Password:</label><br>
    <input type="password" name="adminPassword" required><br><br>

    <button type="submit">Login as Admin</button>
</form>

</body>
</html>