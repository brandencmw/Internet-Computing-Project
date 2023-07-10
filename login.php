<?php
session_start();
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $servername = "localhost";
    $dbname = "cp476";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        echo "Login Failed";
    } else {
        $_SESSION['username'] = $username;
        $_SESSION['password'] = $password;
        echo "Login Succeeded";
    }
    $conn->close();
}


?>