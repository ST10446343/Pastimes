<?php
session_start();
include "DBConn.php";

if (!isset($_SESSION["userID"])) {
    header("Location: login.php");
    exit();
}

$userID = $_SESSION["userID"];

if (isset($_GET['action'])) {
    $action = $_GET['action'];
    
    if ($action == 'add' && isset($_GET['clothesID'])) {
        $clothesID = intval($_GET['clothesID']);
        
        // Check if item already exists in customer's cart
        $check = mysqli_query($conn, "SELECT * FROM tblCart WHERE userID = $userID AND clothesID = $clothesID");
        if (mysqli_num_rows($check) > 0) {
            mysqli_query($conn, "UPDATE tblCart SET quantity = quantity + 1 WHERE userID = $userID AND clothesID = $clothesID");
        } else {
            mysqli_query($conn, "INSERT INTO tblCart (userID, clothesID, quantity) VALUES ($userID, $clothesID, 1)");
        }
        header("Location: cart.php");
        exit();
    }
    
    if ($action == 'delete' && isset($_GET['cartID'])) {
        $cartID = intval($_GET['cartID']);
        mysqli_query($conn, "DELETE FROM tblCart WHERE cartID = $cartID AND userID = $userID");
        header("Location: cart.php");
        exit();
    }
    
    if ($action == 'update' && $_SERVER["REQUEST_METHOD"] == "POST") {
        $cartID = intval($_POST['cartID']);
        $quantity = intval($_POST['quantity']);
        if ($quantity > 0) {
            mysqli_query($conn, "UPDATE tblCart SET quantity = $quantity WHERE cartID = $cartID AND userID = $userID");
        } else {
            mysqli_query($conn, "DELETE FROM tblCart WHERE cartID = $cartID AND userID = $userID");
        }
        header("Location: cart.php");
        exit();
    }
}
?>