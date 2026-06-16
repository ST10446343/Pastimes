<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION["userID"])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION["fullName"];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Pastimes</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="navbar">
    <h2>Pastimes Dashboard</h2>
</div>

<div class="container">
    <h3>Welcome, <?= htmlspecialchars($user) ?> 👋</h3>
    <p>Use the Quick Navigation options below to access platform modules:</p>

    <div style="margin: 20px 0;">
        <a href="products.php"><button style="padding:10px;">🛍️ View & Request Clothes Items</button></a>
        <a href="cart.php"><button style="padding:10px; background-color:orange;">🛒 Open Shopping Cart</button></a>
    </div>
    
    <a href="logout.php">Sign out of platform profile</a>
</div>

</body>
</html>