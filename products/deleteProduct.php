<?php

session_start();
#delete a product from BOTH the products table AND the inventory table
#return null
function deleteProduct($username, $password, $servername, $dbname, $productID, $supplierID){

    $conn = new mysqli($servername, $username, $password, $dbname);

    #collect all necessary data from the product in the products table
    $sql = "SELECT supplierName FROM suppliers WHERE supplierID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $supplierID);
    $stmt->execute();
    $result = $stmt->get_result();
    $result = $result->fetch_assoc();

    #delete the product from the products table
    $sql = "DELETE FROM products WHERE productID = ? AND supplierID = ?"; #delete the item from the products table
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $productID, $supplierID);
    $stmt->execute();

    #update the inventory table
    $sql = "DELETE FROM inventory WHERE productID = ? AND supplierName = ?"; #delete the item from the inventory table
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $productID, $result['supplierName']);
    $stmt->execute();

    $conn->close();

    return;

}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productID = $_POST['productID'];
    $supplierID = $_POST['supplierID'];
    $username = $_SESSION['username'];
    $password = $_SESSION['password'];
    $servername = "localhost";
    $dbname = "cp476";

    deleteProduct($username, $password, $servername, $dbname, $productID, $supplierID);

    echo json_encode(array("finished" => TRUE));
}

?>