<?php
    session_start();
    if(isset($_SESSION['username']) && isset($_SESSION['password'])) {
        header("Location: http://localhost/Internet-Computing-Project", TRUE, 301);
        exit();
    }
?>

<!DOCTYPE html>

<html>

<head>
    <title>Login Page</title>    
    <link rel="stylesheet" href="../styles.css">
    <script src="login.js"></script>
</head>
<body>
    <h2 class="title">Login Page</h2><br>
    <div class="login">
        <form id="login" method="post">
            <label><b>User Name</b></label>
            <br><br>
            <input type="text" name="username" id="username" placeholder="Username">
            <br><br>
            <label><b>Password</b></label>
            <br><br>
            <input type="Password" name="password" id="password" placeholder="Password">
            <br><br><br>
            <input type="submit" name="log" id="log" value="Log In Here">
            <br><br>
        </form>
        <div id="error-box"></div>
    </div>
</body>


</html>