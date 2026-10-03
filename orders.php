<?php

require_once __DIR__ . "/database.php";

if (isset($_GET['status']) && isset($_GET['id'])) {

    $id = intval($_GET['id']);

    $status = $_GET['status'];

    $allowed = [
        'Pending',
        'Preparing',
        'Completed',
        'Cancelled'
    ];

    if (in_array($status, $allowed)) {

        $stmt = $pdo->prepare(
            "UPDATE orders SET status = ? WHERE id = ?"
        );

        $stmt->execute([$status, $id]);
    }

    header("Location: orders.php");

    exit;
}

$orders = $pdo->query(
    "SELECT * FROM orders ORDER BY created_at DESC"
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Orders</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<nav class="navbar">

    <div class="logo">
        ☕ Admin Panel
    </div>

    <div class="nav-links">

        <a href="index.php">Dashboard</a>
        <a href="products.php">Products</a>
        <a href="orders.php">Orders</a>

    </div>

</nav>

<div class="container">

    <h1 class="page-title">
        Customer Orders
    </h1>

    <table class="cart-table">

        <thead>

            <tr>

                <th>Order #</th>
                <th>Customer</th>
                <th>Email</th>
                <th>Total</th>
                <th>Status</th>
                <th>Date</th>

            </tr>

        </thead>

        <tbody>

        <?php while ($order = $orders->fetch()): ?>

            <tr>

                <td>
                    #<?php echo $order['id']; ?>
                </td>

                <td>
                    <?php echo htmlspecialchars(
                        $order['customer_name']
                    ); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars(
                        $order['customer_email']
                    ); ?>
                </td>

                <td>
                    ₱<?php echo number_format(
                        $order['total'],
                        2
                    ); ?>
                </td>

                <td>

                    <a href="orders.php?id=<?php echo $order['id']; ?>&status=Preparing">
                        Preparing
                    </a>

                    |

                    <a href="orders.php?id=<?php echo $order['id']; ?>&status=Completed">
                        Complete
                    </a>

                    |

                    <a href="orders.php?id=<?php echo $order['id']; ?>&status=Cancelled">
                        Cancel
                    </a>

                    <br>

                    <strong>
                        <?php echo htmlspecialchars(
                            $order['status']
                        ); ?>
                    </strong>

                </td>

                <td>
                    <?php echo $order['created_at']; ?>
                </td>

            </tr>

        <?php endwhile; ?>

        </tbody>

    </table>

</div>

</body>
</html>
