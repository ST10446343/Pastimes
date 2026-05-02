<?php

include "DBConn.php";

mysqli_query($conn, "DROP TABLE IF EXISTS tblUser");

$sql = "CREATE TABLE tblUser (
    userID INT AUTO_INCREMENT PRIMARY KEY,
    fullName VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    username VARCHAR(50) NOT NULL UNIQUE,
    passwordHash VARCHAR(250) NOT NULL,
    deliveryAddress VARCHAR(250) NOT NULL,
    userStatus VARCHAR(20) DEFAULT 'Pending'    
)";

if (mysqli_query($conn, $sql)) {
    echo "tblUser created successfully.<br>";
}else {
    die("Error creating tblUser: " . mysqli_error($conn));
}
$file = fopen("userData.txt", "r");

if($file) {
    while(($line = fgets($file)) !== false) { 
        $data = explode(",", trim($line));

        $fullName =$data[0];
        $email = $data[1];
        $username = $data[2];
        $passwordHash = $data[3];
        $deliveryAddress = $data[4];
        $userStatus = $data[5];

        $insert = "INSERT INTO tblUser (fullName, email, username, passwordHash, deliveryAddress, userStatus) VALUES ('$fullName', '$email', '$username', '$passwordHash', '$deliveryAddress', '$userStatus')";

        mysqli_query($conn, $insert);
    }

    fclose($file);
    echo "Data loaded from userData.txt successfully";
} else {
    echo "Could not open userData.txt";
}
?>