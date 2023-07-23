<?php
#search for a supplier by name
#returns an array representing the row in the suppliers table for that supplier
#e.g. [supplierID, supplierName, address, phone, email]
function searchSupplier($servername, $username, $password, $name){
    $conn = new mysqli($servername, $username, $password);
    $sql = "SELECT * FROM suppliers WHERE supplierName LIKE %?%";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $name);
    $result = $stmt->execute();
    return $result->fetch_assoc();

}


#search for a product by name 
#returns nested array containing each row of the table with that product name
#e.g. [[productID, productName, quantity, price, productStatus, supplierName],[productID, productName, quantity, price, productStatus, supplierName]]
function searchInventory($servername, $username, $password, $name){
    $conn = new mysqli($servername, $username, $password);
    $sql = "SELECT * FROM inventory WHERE productName LIKE %?%";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $name);
    $result = $stmt->execute();
    $conn->close();
    $data = array();
    while ($item = $result->fetch_assoc()){
        $data[] = $item;
    }
    return $data;

}

#delete a product from BOTH the products table AND the inventory table
#return null
function deleteProduct($servername, $username, $password, $productID, $supplierID){
    $conn = new mysqli($servername, $username, $password);

    #collect all necessary data from the product in the products table
    $sql = "SELECT * FROM products WHERE productID = ? AND supplierID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $productID, $supplierID);
    $result = $stmt->execute();
    $result = $result->fetch_assoc();

    #delete the product from the products table
    $sql = "DELETE FROM products WHERE productID = ? AND supplierID = ?"; #delete the item from the products table
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $productID, $supplierID);
    $stmt->execute();

    #update the inventory table
    $sql = "DELETE FROM inventory WHERE productID = ? AND supplierName = ?"; #delete the item from the products table
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $productID, $result['supplierName']);
    $stmt->execute();

    return;

}

#buys a product: reduces the quantity values for a product by 1 in both product and inventory tables if the quantity is > 0
#return true if there is still stock for that product, and the quantity was reduced
#return false if that product is out of stock and can't be bought
function buyProduct($servername, $username, $password, $productID, $supplierID) : bool{
    $conn = new mysqli($servername, $username, $password); #connect to DB
    $sql = "SELECT * FROM products WHERE productID = ? AND supplierID = ?"; #collect data about the product
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $productID, $supplierID);
    $result = $stmt->execute();
    $result = $result->fetch_assoc();


    if ($result['quantity'] > 0){
        $sql = "UPDATE products SET quantity = ? WHERE productID = ? AND supplierID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iis", $result['quantity'] -1, $productID, $supplierID);
        $result = $stmt->execute();
        $conn->close();
        updateInventory($servername, $username, $password); #update the quantity in the inventory table
        return true;
    } else { 
        $conn->close();
        return false;
    }

    $conn->close();
    return true;

}

#updates the quantities of items after buying
function updateInventory($servername, $username, $password){
    $sql = "SELECT * FROM products";
    $result = $conn->query($sql);

    while ($item = $result->fetch_assoc()) {
        $productID = $item['productID'];
        $supplierID = $item['supplierID'];
        $quantity = $item['quantity'];


        $sql = "UPDATE inventory SET quantity = ? WHERE productID = ? AND supplierID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $quantity, $productID, $supplierID);
        $stmt->execute();

    }
}

?>