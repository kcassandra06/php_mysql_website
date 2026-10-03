<?php

require_once __DIR__ . "/database.php";

$product_count = $pdo
    ->query("SELECT COUNT(*) AS total FROM products")
    ->fetch()['total'];

$order_count = $pdo
    ->query("SELECT COUNT(*) AS total FROM orders")
    ->fetch()['total'];

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Admin Dashboard</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<nav class="navbar">

    <div class="logo">
        ☕ Admin Panel
    </div>

    <div class="nav-links">

        <a href="index.php">Website</a>
        <a href="products.php">Products</a>
        <a href="orders.php">Orders</a>

    </div>

</nav>

<div class="container">

    <h1 class="page-title">
        Dashboard
    </h1>

    <div class="dashboard">

        <div class="dashboard-card">

            <h2>
                <?php echo $product_count; ?>
            </h2>

            <p>Products</p>

        </div>

        <div class="dashboard-card">

            <h2>
                <?php echo $order_count; ?>
            </h2>

            <p>Orders</p>

        </div>

    </div>

</div>

</body>
</html>
