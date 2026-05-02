<?php

include "DBConn.php";

mysqli_query($conn, "DROP TABLE IF EXISTS tblAorder");
mysqli_query($conn, "DROP TABLE IF EXISTS tblClothes");
mysqli_query($conn, "DROP TABLE IF EXISTS tblAdmin");
mysqli_query($conn, "DROP TABLE IF EXISTS tblUser");

$sqlUser = "CREATE TABLE IF NOT EXISTS tblUser (
    userID INT AUTO_INCREMENT PRIMARY KEY,
    fullName VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    username VARCHAR(50) NOT NULL UNIQUE,
    passwordHash VARCHAR(250) NOT NULL,
    deliveryAddress VARCHAR(250) NOT NULL,
    userStatus VARCHAR(20) DEFAULT 'Pending'    
)";
mysqli_query($conn, $sqlUser);

$sqlAdmin = "CREATE TABLE IF NOT EXISTS tblAdmin (
    adminID INT AUTO_INCREMENT PRIMARY KEY,
    adminName VARCHAR(100) NOT NULL,
    adminEmail VARCHAR(100) NOT NULL UNIQUE,
    adminUsername VARCHAR(50) NOT NULL UNIQUE,
    adminPasswordHash VARCHAR(50) NOT NULL
)";
mysqli_query($conn, $sqlAdmin);

$sqlClothes = "CREATE TABLE IF NOT EXISTS tblClothes (
    clothesID AUTO_INCREMENT PRIMARY KEY,
    sellerID ID INT NOT NULL,
    itemName varchar(100) NOT NULL,
    brand VARCHAR(100) NOT NULL,
    size VARCHAR(20) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    imagePath VARCHAR(250),
    itemStatus VARCHAR(20) DEFAULT 'AVAILABLE',
    FOREIGN KEY (sellerID) REFERENCES tblUser(userID)
)";

mysqli_query($conn, $sqlClothes);

$sqlOrder = "CREATE TABLE IF NOT EXISTS tblAorder(
    orderID INT AUTO_INCREMENT PRIMARY KEY,
    buyerID INT NOT NULL,
    clothesID INT NOT NULL,
    orderDate DATE NOT NULL,
    orderStatus VARCHAR(30) DEFAULT 'PENDING',
    FOREIGN KEY (buyerID) REFERENCES tblUser(userID),
    FOREIGN KEY (clothesID) REFERENCES tblClothes(clothesID)
)";
 mysqli_query($conn, $sqlOrder);

 echo " CothingStore tables have been successfully created.";
 ?>