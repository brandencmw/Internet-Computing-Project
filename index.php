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
</head>

<body>
    <div class="content">
        <?php
            include 'main.php';
            echo $_SESSION['username'];
            echo $_SESSION['password'];
        ?>
    </div>
</body>

</html>