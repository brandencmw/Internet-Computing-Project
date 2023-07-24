<?php
    session_start();
    function getData($conn): void {
        $supplierName = $_GET['name'];

        if($supplierName === "") {
            $sql = "SELECT * FROM suppliers";
            $stmt = $conn->prepare($sql);
        } else {
            $searchTerm = "%" . $supplierName . "%";
            $sql = "SELECT * FROM suppliers WHERE supplierName LIKE ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $searchTerm);
        }

        $stmt->execute();
        $result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Suppliers</title>
    <link rel="stylesheet" href="../styles.css">
    <script src="scripts.js"></script>
</head>

<body>
    <h2>Supplier Information</h2>
    <div class="content">
    <button id="back-button">Back to Main</button>
    <div class="spacer"></div>
    <table>
            <tr>
                <th>Supplier ID</th>
                <th>Supplier Name</th>
                <th>Address</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Options</th>
            </tr>
            <?php foreach($result as $row) {?>
            <tr>
                <td><?php echo $row["supplierID"] ?></td>
                <td><?php echo $row["supplierName"] ?></td>
                <td><?php echo $row["addr"] ?></td>
                <td><?php echo $row["phone"] ?></td>
                <td><?php echo $row["email"] ?></td>
                <td><?php echo "<button id=" . $row["supplierID"] . ">Delete</button>" ?></td>
            </tr>
            <?php } ?>
        </table>
    </div>
</body>

</html>

<?php
    }

    function establishConnection(): mysqli | null {
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