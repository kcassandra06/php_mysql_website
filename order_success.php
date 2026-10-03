<?php

session_start();

$order_id = intval($_GET['id'] ?? 0);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Order Successful</title>

    <link rel="stylesheet"
          href="style.css">

</head>

<body>

<nav class="navbar">

    <div class="logo">
        ☕ Brew Haven
    </div>

</nav>

<div class="success-container">

    <div class="success-icon">
        ✓
    </div>

    <h1>Thank You!</h1>

    <p>
        Your order has been successfully placed.
    </p>

    <p>
        Order Number:
        <strong>#<?php echo $order_id; ?></strong>
    </p>

    <a href="menu.php" class="btn">
        Continue Shopping
    </a>

</div>

</body>
</html>
