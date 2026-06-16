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


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_item"])) {
    $sellerID = $_SESSION["userID"];
    $itemName = trim($_POST["name"]);
    $description = trim($_POST["description"]);
    $brand = trim($_POST["brand"]);
    $price = trim($_POST["price"]);
    
    
    $targetDir = "uploads/";
    $fileName = basename($_FILES["image"]["name"]);
    $targetFilePath = $targetDir . time() . "_" . $fileName; 
    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
    
    if(!empty($fileName)){
        
        $allowTypes = array('jpg','png','jpeg','gif');
        if(in_array(strtolower($fileType), $allowTypes)){
            if(move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)){
                $sql = "INSERT INTO tblClothes 
                        (sellerID, itemName, itemDescription, brand, size, price, imagePath, itemStatus)
                        VALUES (?, ?, ?, ?, 'Medium', ?, ?, 'Pending Approval')";
                
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "isssds", $sellerID, $itemName, $description, $brand, $price, $targetFilePath);
                
                if (mysqli_stmt_execute($stmt)) {
                    $message = "<p class='success'>Item request sent to Administrator for review.</p>";
                } else {
                    $message = "<p class='error'>Database Error: " . mysqli_error($conn) . "</p>";
                }
            } else {
                $message = "<p class='error'>Error uploading your image file.</p>";
            }
        } else {
            $message = "<p class='error'>Only JPG, JPEG, PNG, & GIF files are allowed.</p>";
        }
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

            <?php if (!empty($item["imagePath"]) && file_exists($item["imagePath"])): ?>
            <div class="product-image" style="margin-bottom: 10px;">
                <img src="<?= htmlspecialchars($item["imagePath"]) ?>" alt="<?= htmlspecialchars($item["itemName"]) ?>" style="max-width: 200px; height: auto; border-radius: 4px; display: block;">
            </div>
        <?php else: ?>
            <div style="width: 150px; height: 150px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; color: #aaa; margin-bottom: 10px; font-size: 12px; border-radius: 4px;">
                No Image Provided
            </div>
        <?php endif; ?>

            <strong><?= htmlspecialchars($item["itemName"]) ?></strong><br>
            Brand: <?= htmlspecialchars($item["brand"]) ?><br>
            Price: R<?= htmlspecialchars($item["price"]) ?><br>
            Status: <?= htmlspecialchars($item["itemStatus"]) ?>

            <a href="manageCart.php?action=add&clothesID=<?= $item['clothesID'] ?>">
            <button type="button">Add to Cart</button></a>
       
        </div>
    <?php endwhile; ?>

    <hr>

    <h3>Add New Item</h3>

    <h3>Request to Sell a Clothing Item</h3>

<form method="POST" enctype="multipart/form-data">

    <input type="text" name="name" placeholder="Item Name" required><br><br>
    <textarea name="description" placeholder="Provide clothing item details/condition..." required></textarea><br><br>
    <input type="text" name="brand" placeholder="Brand" required><br><br>
    <input type="number" step="0.01" name="price" placeholder="Price (R)" required><br><br>
    <label>Upload Clothes Photo:</label>
    <input type="file" name="image" accept="image/*" required><br><br>

    <button type="submit" name="add_item">Submit Request to Sell</button>
</form>

    <a href="dashboard.php">Back to Dashboard</a>

</div>

</body>
</html>