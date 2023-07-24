<?php
    session_start();
    if(!isset($_SESSION['username']) || !isset($_SESSION['password'])) {
        header("Location: http://localhost/Internet-Computing-Project/login", TRUE, 301);
        exit();
    }

    function getInventory($conn) {
        $sql = "SELECT * FROM inventory";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Home Page</title>
    <link rel="stylesheet" href="styles.css">
    <script src="scripts.js"></script>
</head>

<body>
    <h2 class="title">Main Page</h2>
    <button id="log-out">Log Out</button>
    <div class="spacer"></div>
    <!-- Suppliers table -->
    <div class="home">
        <h2 class="tabletitle">Suppliers</h2>
        <button id="all-suppliers">View All</button>
        <div class="spacer"></div>
        <form id="supplier-search" method="post">
            <input type="text" id="supplier-name" placeholder="Supplier Name">
            <input type="submit">
        </form>
    </div>

    <div class="spacer"></div>

    <!-- Products table -->
    <div class="home">
        <h2 class="tabletitle">Products</h2>
        <button id="all-products">View All</button>
        <div class="spacer"></div>
        <form id="product-search" method="post">
            <input type="text" id="product-name" placeholder="Product Name">
            <input type="submit">
        </form>
    </div>

    <div class="spacer"></div>

    <!-- Inventory table -->
    <div class="home">
        <h2 class="tabletitle">Inventory</h2>
        <div class="spacer"></div>
        <table>
            <tr>
                <th>Product ID</th>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Product Status</th>
                <th>Supplier Name</th>
            </tr>
            <?php foreach($result as $row) { ?>
            <tr>
                <td><?php echo $row["productID"] ?></td>
                <td><?php echo $row["productName"] ?></td>
                <td><?php echo $row["quantity"] ?></td>
                <td><?php echo $row["price"] ?></td>
                <td><?php echo $row["productStatus"] ?></td>
                <td><?php echo $row["supplierName"] ?></td>
            </tr>
            <?php } ?>
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
    getInventory($conn);
    $conn->close();
?>