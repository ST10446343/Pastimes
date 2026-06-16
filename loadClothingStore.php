<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "DBConn.php";

function runQuery($conn, $sql, $successMessage) {
    if (mysqli_query($conn, $sql)) {
        echo $successMessage . "<br>";
    } else {
        die("SQL ERROR: " . mysqli_error($conn) . "<br><br>Query was:<br>" . $sql);
    }
}

runQuery($conn, "DROP TABLE IF EXISTS tblAorder", "Dropped tblAorder");
runQuery($conn, "DROP TABLE IF EXISTS tblClothes", "Dropped tblClothes");
runQuery($conn, "DROP TABLE IF EXISTS tblAdmin", "Dropped tblAdmin");
runQuery($conn, "DROP TABLE IF EXISTS tblUser", "Dropped tblUser");
runQuery($conn, "DROP TABLE IF EXISTS tblCart", "Dropped tblCart");
runQuery($conn, "DROP TABLE IF EXISTS tblMessage", "Dropped tblMessage");

$sqlUser = "CREATE TABLE tblUser (
    userID INT AUTO_INCREMENT PRIMARY KEY,
    fullName VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    username VARCHAR(50) NOT NULL UNIQUE,
    passwordHash VARCHAR(250) NOT NULL,
    deliveryAddress VARCHAR(250) NOT NULL,
    userStatus VARCHAR(20) DEFAULT 'Pending'
)";

runQuery($conn, $sqlUser, "Created tblUser");

$sqlAdmin = "CREATE TABLE tblAdmin (
    adminID INT AUTO_INCREMENT PRIMARY KEY,
    adminName VARCHAR(100) NOT NULL,
    adminEmail VARCHAR(100) NOT NULL UNIQUE,
    adminUsername VARCHAR(50) NOT NULL UNIQUE,
    adminPasswordHash VARCHAR(50) NOT NULL
)";

runQuery($conn, $sqlAdmin, "Created tblAdmin");

$sqlClothes = "CREATE TABLE tblClothes (
    clothesID INT AUTO_INCREMENT PRIMARY KEY,
    sellerID INT NOT NULL,
    itemName VARCHAR(100) NOT NULL,
    itemDescription TEXT,
    brand VARCHAR(100) NOT NULL,
    size VARCHAR(20) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    imagePath VARCHAR(250),
    itemStatus VARCHAR(20) DEFAULT 'Available',
    FOREIGN KEY (sellerID) REFERENCES tblUser(userID)
)";

runQuery($conn, $sqlClothes, "Created tblClothes");

$sqlOrder = "CREATE TABLE tblAorder (
    orderID INT AUTO_INCREMENT PRIMARY KEY,
    buyerID INT NOT NULL,
    clothesID INT NOT NULL,
    orderDate DATE NOT NULL,
    orderStatus VARCHAR(30) DEFAULT 'Pending',
    FOREIGN KEY (buyerID) REFERENCES tblUser(userID),
    FOREIGN KEY (clothesID) REFERENCES tblClothes(clothesID)
)";

runQuery($conn, $sqlOrder, "Created tblAorder");

$sqlCart = "CREATE TABLE tblCart (
    cartID INT AUTO_INCREMENT PRIMARY KEY,
    userID INT NOT NULL,
    clothesID INT NOT NULL,
    quantity INT DEFAULT 1,
    FOREIGN KEY(userID) REFERENCES tblUser(userID),
    FOREIGN KEY(clothesID) REFERENCES tblClothes(clothesID)
)";

runQuery($conn, $sqlCart, "Created tblCart");

$sqlMessage = "CREATE TABLE tblMessage (
    messageID INT AUTO_INCREMENT PRIMARY KEY,
    senderID INT NOT NULL,
    receiverID INT NOT NULL,
    messageText TEXT NOT NULL,
    messageDate DATETIME DEFAULT CURRENT_TIMESTAMP
)";

runQuery($conn, $sqlMessage, "Created tblMessage");

echo "<br><strong>ClothingStore tables have been successfully created.</strong>";
?>