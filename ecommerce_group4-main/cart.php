<?php
$servername = "localhost";
$username = "root";
$password = ""; 
$dbname = "e-commerce";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = "";
$product_id = "";
$username = "";
$name = "";
$description = "";
$price = "";
$image = "";
$quantity = "";

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (!isset($_GET["user_id"]) || empty($_GET["user_id"]) || !isset($_GET["product_id"]) || empty($_GET["product_id"])) {
        header("Location: index.php");
        exit;
    }

    $user_id = intval($_GET["user_id"]);
    $product_id = intval($_GET["product_id"]);

    $sql = "SELECT products.name, products.description, products.price, products.image, cart.quantity
            FROM `cart`
            INNER JOIN `users` ON users.user_id = cart.user_id
            INNER JOIN `products` ON products.product_id = cart.product_id
            WHERE cart.user_id = ? AND cart.product_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $user_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row) {
        header("Location: index.php");
        exit;
    }

    $name = $row["name"];
    $description = $row["description"];
    $price = $row["price"];
    $image = $row["image"];
    $quantity = $row["quantity"];
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Product Details</title>
</head>
<body class="p-3 m-0 border-0 bd-example m-0 border-0">

<div class="card" style="width: 18rem;">
    <img src="<?php echo htmlspecialchars($image); ?>" class="card-img-top" alt="Product Image">
    <div class="card-body">
        <h5 class="card-title"><p>name:</p><?php echo htmlspecialchars($name); ?></h5>
        <p class="card-text"><p>description:</p><?php echo htmlspecialchars($description); ?></p>
        <p class="card-text"><p>Price:</p> <?php echo htmlspecialchars($price); ?></p>
        <p class="card-text"><p>Quantity:</p> <?php echo htmlspecialchars($quantity); ?></p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
