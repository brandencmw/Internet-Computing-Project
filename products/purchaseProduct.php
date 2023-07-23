<?php

session_start();

#updates the quantities of items after buying
function updateInventory($conn, $productID, $supplierID, $quantity){
    #collect all necessary data from the product in the products table
    $sql = "SELECT supplierName FROM suppliers WHERE supplierID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $supplierID);
    $stmt->execute();
    $result = $stmt->get_result();
    $result = $result->fetch_assoc();

    $supplierName = $result['supplierName'];

    $sql = "UPDATE inventory SET quantity = ? WHERE productID = ? AND supplierName = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $quantity, $productID, $supplierName);
    $stmt->execute();

    return;

}

function purchaseProduct($username, $password, $servername, $dbname, $productID, $supplierID) {
    $conn = new mysqli($servername, $username, $password, $dbname); #connect to DB
    $sql = "SELECT * FROM products WHERE productID = ? AND supplierID = ?"; #collect data about the product
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $productID, $supplierID);
    $stmt->execute();
    $result = $stmt->get_result();
    $result = $result->fetch_assoc();

    if ($result['quantity'] > 0){
        $newQuantity = $result['quantity'] - 1;
        $sql = "UPDATE products SET quantity = ? WHERE productID = ? AND supplierID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $newQuantity, $productID, $supplierID);
        $result = $stmt->execute();
        updateInventory($conn, $productID, $supplierID, $newQuantity); #update the quantity in the inventory table
    } else { 
        $conn->close();
        return false;
    }

    $conn->close();
    return true;
}


if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productID = $_POST['productID'];
    $supplierID = $_POST['supplierID'];
    $username = $_SESSION['username'];
    $password = $_SESSION['password'];
    $servername = "localhost";
    $dbname = "cp476";

    $success = purchaseProduct($username, $password, $servername, $dbname, $productID, $supplierID);

    echo json_encode(array("finished" => $success));
}

?>