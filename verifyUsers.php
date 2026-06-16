<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include "DBConn.php";

// Verify admin login session security
if (!isset($_SESSION["adminID"])) {
    header("Location: adminLogin.php");
    exit();
}

$message = "";

if (isset($_GET["verify"])) {
    $userID = intval($_GET["verify"]);
    $sql = "UPDATE tblUser SET userStatus = 'Verified' WHERE userID = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $userID);
    if (mysqli_stmt_execute($stmt)) {
        $message = "User account verified successfully.";
    }
}

if (isset($_GET["delete"])) {
    $userID = intval($_GET["delete"]);
    $sql = "DELETE FROM tblUser WHERE userID = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $userID);
    if (mysqli_stmt_execute($stmt)) {
        $message = "User account deleted successfully.";
    }
}

if (isset($_GET["approve_item"])) {
    $clothesID = intval($_GET["approve_item"]);
    mysqli_query($conn, "UPDATE tblClothes SET itemStatus = 'Available' WHERE clothesID = $clothesID");
    $message = "Clothing item approved and listed on the live marketplace store!";
}

if (isset($_GET["delete_item"])) {
    $clothesID = intval($_GET["delete_item"]);
    mysqli_query($conn, "DELETE FROM tblClothes WHERE clothesID = $clothesID");
    $message = "Clothing item entry successfully deleted.";
}


if (isset($_GET["complete_delivery"])) {
    $clothesID = intval($_GET["complete_delivery"]);
    mysqli_query($conn, "UPDATE tblClothes SET itemStatus = 'Delivered' WHERE clothesID = $clothesID");
    $message = "Order shipment verified and archived successfully!";
}

$usersResult = mysqli_query($conn, "SELECT * FROM tblUser");

$clothesResult = mysqli_query($conn, "
    SELECT c.*, u.username 
    FROM tblClothes c 
    JOIN tblUser u ON c.sellerID = u.userID
    WHERE c.itemStatus IN ('Available', 'Pending Approval')
");

$deliveryResult = mysqli_query($conn, "
    SELECT c.*, u.username as sellerName, 
           (SELECT username FROM tblUser WHERE userID = ao.buyerID) as buyerName
    FROM tblClothes c
    JOIN tblAorder ao ON c.clothesID = ao.clothesID
    JOIN tblUser u ON c.sellerID = u.userID
    WHERE c.itemStatus = 'Pending Delivery'
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage System Workspace - Pastimes</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div style="padding: 20px; max-width: 1100px; margin: 0 auto; font-family: sans-serif;">

    <h1>Admin Operations Dashboard</h1>
    <p>Logged in security executive: <strong><?php echo htmlspecialchars($_SESSION["adminName"]); ?></strong></p>

    <?php if (!empty($message)): ?>
        <p style="color:green; font-weight:bold; background:#e8f5e9; padding:10px; border-radius:4px;"><?php echo $message; ?></p>
    <?php endif; ?>

    <hr style="margin: 20px 0;">

    <h2>Manage Users</h2>
    <table border="1" cellpadding="8" style="width:100%; border-collapse:collapse; margin-bottom:40px;">
        <tr style="background-color: #f4f4f4;">
            <th>User ID</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Username</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php if(mysqli_num_rows($usersResult) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($usersResult)) { ?>
                <tr>
                    <td><?php echo $row["userID"]; ?></td>
                    <td><?php echo htmlspecialchars($row["fullName"]); ?></td>
                    <td><?php echo htmlspecialchars($row["email"]); ?></td>
                    <td><?php echo htmlspecialchars($row["username"]); ?></td>
                    <td style="font-weight: bold; color: <?= ($row["userStatus"] == 'Verified') ? 'green' : 'orange' ?>;"><?php echo htmlspecialchars($row["userStatus"]); ?></td>
                    <td>
                        <?php if ($row["userStatus"] == "Pending") { ?>
                            <a href="verifyUsers.php?verify=<?php echo $row["userID"]; ?>"><button style="cursor:pointer;">Verify</button></a>
                        <?php } else { ?>
                            <span style="color:gray; font-size:13px;">Active Status</span>
                        <?php } ?>
                        |
                        <a href="verifyUsers.php?delete=<?php echo $row["userID"]; ?>" onclick="return confirm('Permanently wipe user profile account?');">
                            <button style="color:red; cursor:pointer;">Delete</button>
                        </a>
                    </td>
                </tr>
            <?php } ?>
        <?php else: ?>
            <tr><td colspan="6" align="center" style="color:gray;">No user registration accounts exist in the system database.</td></tr>
        <?php endif; ?>
    </table>

    <h1>Manage Clothing</h1>
    <table border="1" cellpadding="8" style="width:100%; border-collapse:collapse; margin-bottom:40px;">
        <tr style="background-color: #f4f4f4;">
            <th>ID</th>
            <th>Image</th>
            <th>Item</th>
            <th>Brand</th>
            <th>Price</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php if (mysqli_num_rows($clothesResult) > 0): ?>
            <?php while ($item = mysqli_fetch_assoc($clothesResult)) { ?>
                <tr>
                    <td><?php echo $item["clothesID"]; ?></td>
                    <td><img src="<?php echo htmlspecialchars($item['imagePath']); ?>" width="50" alt="No image file available" style="border-radius:4px;"></td>
                    <td style="font-weight:bold;"><?php echo htmlspecialchars($item["itemName"]); ?></td>
                    <td><?php echo htmlspecialchars($item["brand"]); ?></td>
                    <td>R<?php echo htmlspecialchars($item["price"]); ?></td>
                    <td style="font-weight:bold; color: <?php echo ($item['itemStatus'] == 'Available') ? 'green' : 'orange'; ?>;"><?php echo htmlspecialchars($item["itemStatus"]); ?></td>
                    <td>
                        <?php if ($item["itemStatus"] == "Pending Approval") { ?>
                            <a href="verifyUsers.php?approve_item=<?php echo $item["clothesID"]; ?>">
                                <button style="background-color:green; color:white; border:none; padding:5px 10px; cursor:pointer; border-radius:3px;">Approve</button>
                            </a> |
                        <?php } ?>
                        <a href="verifyUsers.php?delete_item=<?php echo $item["clothesID"]; ?>" onclick="return confirm('Delete clothing listing entry?');">
                            <button style="cursor:pointer; padding:5px 10px;">Delete</button>
                        </a>
                    </td>
                </tr>
            <?php } ?>
        <?php else: ?>
            <tr><td colspan="7" align="center" style="color:gray;">No clothes matching active store parameters found.</td></tr>
        <?php endif; ?>
    </table>

    <h2>Order Verification & Shipments Tracking</h2>
    <table border="1" cellpadding="8" style="width:100%; border-collapse:collapse; margin-bottom:30px;">
        <tr style="background-color: #f4f4f4;">
            <th>Image</th>
            <th>Item Description Details</th>
            <th>Price Value</th>
            <th>Seller Name</th>
            <th>Buyer Destination</th>
            <th>Logistics Status</th>
            <th>System Quality Checks Action</th>
        </tr>
        <?php if ($deliveryResult && mysqli_num_rows($deliveryResult) > 0): ?>
            <?php while ($order = mysqli_fetch_assoc($deliveryResult)) { ?>
                <tr>
                    <td><img src="<?php echo htmlspecialchars($order['imagePath']); ?>" width="50" style="border-radius:4px;"></td>
                    <td><strong><?php echo htmlspecialchars($order["itemName"]); ?></strong></td>
                    <td>R<?php echo htmlspecialchars($order["price"]); ?></td>
                    <td><?php echo htmlspecialchars($order["sellerName"]); ?></td>
                    <td><?php echo htmlspecialchars($order["buyerName"]); ?></td>
                    <td style="color:blue; font-weight:bold;"><?php echo htmlspecialchars($order["itemStatus"]); ?></td>
                    <td>
                        <a href="verifyUsers.php?complete_delivery=<?php echo $order["clothesID"]; ?>">
                            <button style="background-color:blue; color:white; border:none; padding:6px 12px; cursor:pointer; border-radius:4px; font-weight:bold;">
                                Confirm Delivered & Safe Condition
                            </button>
                        </a>
                    </td>
                </tr>
            <?php } ?>
        <?php else: ?>
            <tr><td colspan="7" align="center" style="color:gray;">No pending post-purchase item shipments require delivery checks.</td></tr>
        <?php endif; ?>
    </table>

    <br><br>
    <a href="adminMessages.php">
        <button style="padding:12px; background-color:black; color:white; border:none; border-radius:4px; font-weight:bold; cursor:pointer;">
            Open Admin Communication Center
        </button>
    </a>
    <br><br>
    <a href="adminLogin.php" style="color:blue; font-weight:bold; text-decoration:none;">← Log Out / Return to Admin Login Screen</a>

</div>

</body>
</html>