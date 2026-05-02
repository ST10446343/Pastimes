<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include "DBConn.php";

if (!isset($_SESSION["adminID"])) {
    header("Location: adminLogin.php");
    exit();
}

$message = "";

if (isset($_GET["verify"])) {
    $userID = $_GET["verify"];

    $sql = "UPDATE tblUser SET userStatus = 'Verified' WHERE userID = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $userID);

    if (mysqli_stmt_execute($stmt)) {
        $message = "User verified successfully.";
    }
}

if (isset($_GET["delete"])) {
    $userID = $_GET["delete"];

    $sql = "DELETE FROM tblUser WHERE userID = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $userID);

    if (mysqli_stmt_execute($stmt)) {
        $message = "User deleted successfully.";
    }
}

$result = mysqli_query($conn, "SELECT * FROM tblUser");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Verify Users - Pastimes</title>
</head>
<body>

<h2>Admin Panel - Verify Users</h2>

<p>Logged in as Admin: <?php echo $_SESSION["adminName"]; ?></p>

<p style="color:green;"><?php echo $message; ?></p>

<table border="1" cellpadding="8">
    <tr>
        <th>User ID</th>
        <th>Full Name</th>
        <th>Email</th>
        <th>Username</th>
        <th>Delivery Address</th>
        <th>Status</th>
        <th>Action</th>
    </tr>

    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?php echo $row["userID"]; ?></td>
            <td><?php echo $row["fullName"]; ?></td>
            <td><?php echo $row["email"]; ?></td>
            <td><?php echo $row["username"]; ?></td>
            <td><?php echo $row["deliveryAddress"]; ?></td>
            <td><?php echo $row["userStatus"]; ?></td>
            <td>
                <?php if ($row["userStatus"] == "Pending") { ?>
                    <a href="verifyUsers.php?verify=<?php echo $row["userID"]; ?>">Verify</a>
                <?php } else { ?>
                    Verified
                <?php } ?>

                |
                <a href="verifyUsers.php?delete=<?php echo $row["userID"]; ?>"
                   onclick="return confirm('Are you sure you want to delete this user?');">
                   Delete
                </a>
            </td>
        </tr>
    <?php } ?>

</table>

<br>
<a href="adminLogin.php">Back to Admin Login</a>

</body>
</html>