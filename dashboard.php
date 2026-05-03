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

    <p>This is your dashboard.</p>

    <a href="products.php"><button>View Items</button></a>
    <a href="products.php"><button>Add Clothing Item</button></a>

    <a href="logout.php">Logout</a>
</div>

</body>
</html>