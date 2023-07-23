<?php
    session_start();
    if(!isset($_SESSION['username']) || !isset($_SESSION['password'])) {
        header("Location: http://localhost/Internet-Computing-Project/login", TRUE, 301);
        exit();
    }
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
</body>

</html>