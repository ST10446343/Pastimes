<?php

$server = "127.0.0.1";
$username = "root";
$password = "";
$database = "ClothingStore";

$conn = mysqli_connect($server, $username, $password, $database);

if(!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>