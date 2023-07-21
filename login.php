<?php
session_start();

#used to check if the database has already been created
#returns true or false
#conn-> the mysqli connection, 
#databaseName-> the name of the mySQL database
function findDB($servername, $username, $password, $databaseName) : bool {
    $conn = new mysqli($servername, $username, $password);
    $sql = "SHOW DATABASES LIKE '$databaseName'";
    $result = $conn->query($sql);
    $conn->close();
    return $result->num_rows > 0;
}

#create the database
#conn-> the mysqli connection
#returns true or false if the connection was successful or not
function createDB($servername, $username, $password) : bool{
    $conn = new mysqli($servername, $username, $password);
    $sql = file_get_contents('database.sql');
    if($conn->multi_query($sql) === true) {
        $conn->close();
        return true;
    } else {

        return false;
        $conn->close();
    }
}

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



#fill the database tables if they have not been filled already
#conn-> the mysqli connection
function initDB($servername, $username, $password, $databaseName) {
    $supplierFile = 'SupplierFile.csv'; #csv files containing the data
    $productFile = 'ProductFile.csv';

    $conn = new mysqli($servername, $username, $password, $databaseName);

    #check if the suppliers table is already populated
    $sql = "SELECT COUNT(*) FROM suppliers";
    $result = $conn->query($sql);
    $count = $result->fetch_array()[0];
    if($count > 0) {
        $populated= true;
    } else {
        $populated = false;
    }

    if ($populated !== true && ($handle = fopen($supplierFile, "r")) !== false) {
        while(($result = fgetcsv($handle, 500, ',')) !== false){ #loop through csv file lines
            $supplierID = $result[0];
            $supplierName = $result[1];
            $addr = $result[2];
            $phone = $result[3];
            $email = $result[4];

            #insert values into table with prepared statement
            $sql = "INSERT INTO suppliers (supplierID, supplierName, addr, phone, email) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("issss", $supplierID, $supplierName, $addr, $phone, $email);
            $stmt->execute();
            
        }
    }

    #check if the product table is already populated
    $sql = "SELECT COUNT(*) FROM products";
    $result = $conn->query($sql);
    $count = $result->fetch_array()[0];
    if($count > 0) {
        $populated= true;
    } else {
        $populated = false;
    }

    #if the table isn't populated, fill it
    if (!$populated && ($handle = fopen($productFile, "r")) !== false) {
        while(($result = fgetcsv($handle, 500, ',')) !== false){ #loop through csv file lines
            $productID = $result[0];    
            $productName = $result[1];
            $productDescription = $result[2];
            $price = str_replace('$', '', $result[3]);
            $price = (float)$price;
            $quantity = $result[4];
            $productStatus = $result[5];
            $supplierID = $result[6];

            #insert values into table with prepared statement
            $sql = "INSERT INTO products (productID, productName, productDescription, price, quantity, productStatus, supplierID) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("issdisi", $productID, $productName, $productDescription, $price, $quantity, $productStatus, $supplierID);
            $stmt->execute();

        }
    }

    #check if the inventory table is already populated
    $sql = "SELECT COUNT(*) FROM inventory";
    $result = $conn->query($sql);
    $count = $result->fetch_array()[0];
    if($count > 0) {
        $populated= true;
    } else {
        $populated = false;
    }

    if (!$populated){
        $sql = "SELECT * FROM products";
        $result = $conn->query($sql);

        while ($item = $result->fetch_assoc()) {
            $productID = $item['productID'];
            $productName = $item['productName'];
            $quantity = $item['quantity'];
            $price = $item['price'];
            $status = $item['productStatus'];

            $sql = "SELECT * FROM suppliers WHERE supplierID = " . $item['supplierID'];
            $supplier = $conn->query($sql);
            $row = $supplier->fetch_assoc();

            $supplierName = $row['supplierName'];

            $sql = "INSERT INTO inventory (productID, productName, quantity, price, productStatus, supplierName) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isidss", $productID, $productName, $quantity, $price, $status, $supplierName);
            $stmt->execute();

        }
    }
}

function testConnect($servername, $username, $password): bool {
    try {
        $conn = new mysqli($servername, $username, $password);
        if ($conn->connect_errno) {
            return false;
        }
        $conn->close();
    } catch(Exception $e) {
        return false;
    }
    return true;
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $servername = "localhost";
    $dbname = "cp476";

    $success = testConnect($servername, $username, $password);

    if($success) {
        $_SESSION['username'] = $username;
        $_SESSION['password'] = $password;

        $db_exists = findDb($servername, $_SESSION['username'], $_SESSION['password'], $dbname);
        if(!$db_exists) {
            createDb($servername, $_SESSION['username'], $_SESSION['password'], $dbname);
        }
        initDB($servername, $_SESSION['username'], $_SESSION['password'], $dbname);
        $message = "success";
    } else {
        $message = "incorrect info";
    }
    echo json_encode(array("loggedIn" => $success, "message" => $message));
}


?>