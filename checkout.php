<?php

session_start();

require_once __DIR__ . "/database.php";

if (empty($_SESSION['cart'])) {

    header("Location: menu.php");
    exit;

}

$cart = $_SESSION['cart'];
$selectedIds = $_SESSION['selected_cart'] ?? array_keys($cart);
$checkoutCart = [];
foreach ($selectedIds as $id) {
    $id = (int) $id;
    if (isset($cart[$id])) {
        $checkoutCart[$id] = max(1, (int) $cart[$id]);
    }
}
if (!$checkoutCart) {
    header("Location: cart.php?select=1");
    exit;
}

$total = 0;

foreach ($checkoutCart as $product_id => $quantity) {

    $product_id = intval($product_id);

    $stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();

    if ($product) {

        $total += $product['price'] * $quantity;

    }

}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    if ($name === '' || $email === '') {

        $error = "Please fill in all required fields.";

    } else {

        $stmt = $pdo->prepare(
            "INSERT INTO orders
            (customer_name, customer_email, customer_phone, address, total)
            VALUES (?, ?, ?, ?, ?)"
        );

        $stmt->execute([$name, $email, $phone, $address, $total]);
        $order_id = $pdo->lastInsertId();

        $item_stmt = $pdo->prepare(
            "INSERT INTO order_items
            (order_id, product_id, quantity, price)
            VALUES (?, ?, ?, ?)"
        );

        foreach ($checkoutCart as $product_id => $quantity) {

            $product_id = intval($product_id);

            $price_stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
            $price_stmt->execute([$product_id]);
            $product = $price_stmt->fetch();

            if (!$product) {
                continue;
            }

            $price = $product['price'];

            $item_stmt->execute([$order_id, $product_id, $quantity, $price]);
        }

        foreach (array_keys($checkoutCart) as $orderedId) {
            unset($_SESSION['cart'][$orderedId]);
        }
        unset($_SESSION['selected_cart']);

        header(
            "Location: order_success.php?id=" . $order_id
        );

        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Checkout</title>

    <link rel="stylesheet"
          href="style.css">

</head>

<body>

<nav class="navbar">

    <div class="logo">
        ☕ Brew Haven
    </div>

    <div class="nav-links">

        <a href="index.php">Home</a>
        <a href="menu.php">Menu</a>
        <a href="cart.php">Cart</a>

    </div>

</nav>

<div class="form-container checkout-form">

    <h1>Checkout</h1>

    <p>
        Order Total:
        <strong>
            ₱<?php echo number_format($total, 2); ?>
        </strong>
    </p>

    <?php if (isset($error)): ?>

        <div class="error">
            <?php echo htmlspecialchars($error); ?>
        </div>

    <?php endif; ?>

    <form method="POST">

        <label>Full Name *</label>

        <input
            type="text"
            name="name"
            required
        >

        <label>Email *</label>

        <input
            type="email"
            name="email"
            required
        >

        <label>Phone</label>

        <input
            type="text"
            name="phone"
        >

        <label>Delivery Address</label>

        <textarea
            name="address"
            rows="4"
        ></textarea>

        <button type="submit" class="btn">
            Place Order
        </button>

    </form>

</div>

</body>
</html>
