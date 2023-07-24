<?php
session_start();
#delete a product from BOTH the products table AND the inventory table
#return null
function deleteSupplier($username, $password, $servername, $dbname, $supplierID): void {

    $conn = new mysqli($servername, $username, $password, $dbname);

    #get corresponding supplier name
    $sql = "SELECT supplierName FROM suppliers WHERE supplierID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $supplierID);
    $stmt->execute();
    $result = $stmt->get_result();
    $result = $result->fetch_assoc();

    #delete the supplier from the suppliers table
    $sql = "DELETE FROM suppliers WHERE supplierID = ?"; #delete the item from the products table
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $supplierID);
    $stmt->execute();

    #delete all inventory rows matching supplier name
    $sql = "DELETE FROM inventory WHERE supplierName = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $result["supplierName"]);
    $stmt->execute();

    $conn->close();

    return;

}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplierID = $_POST['supplierID'];
    $username = $_SESSION['username'];
    $password = $_SESSION['password'];
    $servername = "localhost";
    $dbname = "cp476";

    deleteSupplier($username, $password, $servername, $dbname, $supplierID);

    echo json_encode(array("finished" => TRUE));
}

?>