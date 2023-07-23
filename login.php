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
        echo "Database creation success.";
        return true;
    } else {
        echo "Database creation fail." . $conn->error;
        return false;
    }
}


#fill the database tables if they have not been filled already
#conn-> the mysqli connection
function initDB($servername, $username, $password, $databaseName) {
    $supplierFile = 'SupplierFile.csv'; #csv files containing the data
    $productFile = 'ProductFile.csv';

    $conn = new mysqli($servername, $username, $password, $databaseName);

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
            $price = $result[3];
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
    }
    echo json_encode(array("loggedIn" => $success));
}


?>