<?php
    session_start();
    function getData($conn) {
        $productName = $_GET['name'];

        if($productName === "") {
            $sql = "SELECT productID, productName, productDescription, price, quantity, productStatus, supplierID FROM products";
            $stmt = $conn->prepare($sql);
        } else {
            $searchTerm = "%" . $productName . "%";
            $sql = "SELECT productID, productName, productDescription, price, quantity, productStatus, supplierID FROM products WHERE productName LIKE ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $searchTerm);
        }

        $stmt->execute();
        $result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Products</title>
    <link rel="stylesheet" href="../styles.css">
    <script src="scripts.js"></script>
</head>


<body>
    <div class="content">
        <table>
            <tr>
                <th>Product ID</th>
                <th>Product Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Status</th>
                <th>Supplier ID</th>
                <th>Options</th>
            </tr>
            <?php
                foreach($result as $row) {
            ?>
            <tr>
                <td><?php echo $row["productID"] ?></td>
                <td><?php echo $row["productName"] ?></td>
                <td><?php echo $row["productDescription"] ?></td>
                <td><?php echo $row["price"] ?></td>
                <td><?php echo $row["quantity"] ?></td>
                <td><?php echo $row["productStatus"] ?></td>
                <td><?php echo $row["supplierID"] ?></td>
                <td><?php 
                    $buttonID = $row["productID"] . "," . $row["supplierID"];
                    echo "<button id=d" . $buttonID . " class=delete>Delete</button>";
                    echo "<button id=p" . $buttonID . " class=purchase>Purchase</button>";
                ?></td>
            </tr>
            <?php
                }
            ?>
        </table>
    </div>
</body>

</html>

<?php

    }

    function establishConnection() {
        $username = $_SESSION["username"];
        $password = $_SESSION["password"];
        $servername = "localhost";
        $dbname = "cp476";
        $conn = new mysqli($servername, $username, $password, $dbname);
        if($conn->connect_errno) {
            die("Failed to connect " . $conn->connect_errno);
        }

        return $conn;
    }

    $conn = establishConnection();
    getData($conn);
    $conn->close();

?>