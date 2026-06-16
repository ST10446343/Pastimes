<?php
session_start();
include "DBConn.php";

// Verify user login session status
if (!isset($_SESSION["userID"])) {
    header("Location: login.php");
    exit();
}

$userID = $_SESSION["userID"];
$message = "";

// --- FUNCTIONAL BACKEND CHECKOUT HANDLER ---
if (isset($_POST['checkout'])) {
    $cartItems = mysqli_query($conn, "SELECT * FROM tblCart WHERE userID = $userID");
    
    if (mysqli_num_rows($cartItems) > 0) {
        $today = date("Y-m-d");
        while ($row = mysqli_fetch_assoc($cartItems)) {
            $cID = $row['clothesID'];
            // Insert into order record
            mysqli_query($conn, "INSERT INTO tblAorder (buyerID, clothesID, orderDate, orderStatus) VALUES ($userID, $cID, '$today', 'Pending')");
            // Mark item as Pending delivery so it moves out of standard active inventory views
            mysqli_query($conn, "UPDATE tblClothes SET itemStatus = 'Pending Delivery' WHERE clothesID = $cID");
        }
        // Wipe cart for this user
        mysqli_query($conn, "DELETE FROM tblCart WHERE userID = $userID");
        $message = "<p style='color:green; font-weight:bold;'>Checkout successful! Your items are pending validation.</p>";
    } else {
        $message = "<p style='color:red; font-weight:bold;'>Your cart is empty.</p>";
    }
}

// --- FETCH DYNAMIC CART DISPLAY DATA ---
$query = "SELECT c.cartID, c.quantity, p.itemName, p.brand, p.price 
          FROM tblCart c 
          JOIN tblClothes p ON c.clothesID = p.clothesID 
          WHERE c.userID = $userID";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Past Times - Shopping Cart</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="container" style="max-width: 800px; margin: 0 auto; padding: 20px; font-family: sans-serif;">

    <h1>Shopping Cart</h1>
    
    <?= $message ?>
    
    <?php if (mysqli_num_rows($result) > 0): ?>
        <table border="1" cellpadding="10" style="width:100%; border-collapse:collapse; margin-bottom:20px;">
            <tr style="background-color: #f8f9fa;">
                <th>Item</th>
                <th>Brand</th> <th>Price</th>
                <th>Quantity</th>
                <th>Action</th>
            </tr>

            <?php 
            $total = 0;
            while ($item = mysqli_fetch_assoc($result)): 
                $total += ($item['price'] * $item['quantity']);
            ?>
            <tr>
                <td style="font-weight: bold;"><?= htmlspecialchars($item['itemName']) ?></td>
                <td><?= htmlspecialchars($item['brand']) ?></td>
                <td>R<?= number_format($item['price'], 2) ?></td>
                <td>
                    <form action="manageCart.php?action=update" method="POST" style="margin:0; display:inline;">
                        <input type="hidden" name="cartID" value="<?= $item['cartID'] ?>">
                        <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" style="width:50px; padding:4px;">
                </td>
                <td>
                        <button type="submit" style="cursor:pointer; padding:4px 8px;">Update</button>
                    </form>
                    
                    <a href="manageCart.php?action=delete&cartID=<?= $item['cartID'] ?>">
                        <button type="button" style="cursor:pointer; padding:4px 8px; color:red;">Remove</button>
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
            
            <tr style="background-color: #f1f3f5; font-weight: bold;">
                <td colspan="3" align="right">Total:</td>
                <td colspan="2" style="color: green; font-size: 16px;">R<?= number_format($total, 2) ?></td>
            </tr>
        </table>

        <br>

        <button type="button" onclick="location.href='products.php'" style="padding: 10px 15px; margin-right: 10px; cursor: pointer;">
            Continue Shopping
        </button>

        <form method="POST" style="display:inline;">
            <button type="submit" name="checkout" style="background-color: green; color: white; padding: 10px 15px; border: none; font-weight: bold; cursor: pointer;">
                Checkout
            </button>
        </form>

    <?php else: ?>
        <p style="color: gray; font-style: italic;">Your shopping cart is empty.</p>
        <br>
        <button type="button" onclick="location.href='products.php'" style="padding: 10px 15px; cursor: pointer;">
            Browse Clothing Items
        </button>
    <?php endif; ?>

</div>

</body>
</html>