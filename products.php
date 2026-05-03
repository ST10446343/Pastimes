<?php
session_start();
include "DBConn.php";

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION["userID"])) {
    header("Location: login.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sellerID = $_SESSION["userID"];
    $itemName = trim($_POST["name"]);
    $brand = trim($_POST["brand"]);
    $price = trim($_POST["price"]);

    $sql = "INSERT INTO tblClothes 
            (sellerID, itemName, brand, size, price, imagePath, itemStatus)
            VALUES (?, ?, ?, 'Medium', ?, '', 'Available')";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "issd", $sellerID, $itemName, $brand, $price);

    if (mysqli_stmt_execute($stmt)) {
        $message = "<p class='success'>Item added successfully.</p>";
    } else {      
        $message = "<p class='error'>Item could not be added: " . mysqli_error($conn) . "</p>";
    }
}

$result = mysqli_query($conn, "SELECT * FROM tblClothes WHERE itemStatus = 'Available'");

if (!$result) {
    die("SQL Error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Products - Pastimes</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .product {
            border: 1px solid #ccc;
            padding: 10px;
            margin: 10px 0;
            border-radius: 8px;
        }
    </style>
</head>
<body>

<div class="navbar">
    <h2>Pastimes Marketplace</h2>
</div>

<div class="container">

    <h3>Clothing Items</h3>

    <?= $message ?>

    <?php while ($item = mysqli_fetch_assoc($result)): ?>
        <div class="product">
            <strong><?= htmlspecialchars($item["itemName"]) ?></strong><br>
            Brand: <?= htmlspecialchars($item["brand"]) ?><br>
            Price: R<?= htmlspecialchars($item["price"]) ?><br>
            Status: <?= htmlspecialchars($item["itemStatus"]) ?>
        </div>
    <?php endwhile; ?>

    <hr>

    <h3>Add New Item</h3>

    <form method="POST">
        <input type="text" name="name" placeholder="Item Name" required>
        <input type="text" name="brand" placeholder="Brand" required>
        <input type="number" step="0.01" name="price" placeholder="Price" required>

        <button type="submit">Add Item</button>
    </form>

    <a href="dashboard.php">Back to Dashboard</a>

</div>

</body>
</html>