<?php
session_start();
require_once __DIR__ . '/database.php';

$cart = $_SESSION['cart'] ?? [];
$selected = $_SESSION['selected_cart'] ?? array_keys($cart);

$items = [];

if ($cart) {
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');

    foreach ($cart as $id => $quantity) {

        $stmt->execute([(int)$id]);
        $product = $stmt->fetch();

        if (!$product) {
            unset($_SESSION['cart'][$id]);
            continue;
        }

        $quantity = max(1, (int)$quantity);

        // Make sure selected state is valid
        $isSelected = in_array((string)$id, array_map('strval', $selected), true);

        $product['quantity'] = $quantity;
        $product['subtotal'] = (float)$product['price'] * $quantity;
        $product['selected'] = $isSelected;

        $items[] = $product;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Your Cart | Brew Haven</title>

    <link rel="stylesheet" href="style.css">

    <style>

        .select-box {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .quantity-control {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .quantity-btn {
            width: 32px;
            height: 32px;
            border: 1px solid #ccc;
            background: #fff;
            cursor: pointer;
            border-radius: 6px;
            font-size: 18px;
        }

        .quantity-btn:hover {
            background: #f3f3f3;
        }

        .quantity-number {
            min-width: 25px;
            text-align: center;
            font-weight: 600;
        }

        .selected-total {
            margin-bottom: 15px;
        }

        .selected-total strong {
            font-size: 24px;
        }

        .select-all {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 15px;
        }

        .checkout-disabled {
            opacity: .5;
            pointer-events: none;
        }

    </style>

</head>

<body>

<nav class="navbar">

    <a class="logo" href="index.php">
        ☕ Brew Haven
    </a>

    <div class="nav-links">

        <a href="index.php">Home</a>

        <a href="menu.php">Menu</a>

        <a class="active" href="cart.php">
            Cart
            <span class="nav-count">
                <?= array_sum($_SESSION['cart'] ?? []) ?>
            </span>
        </a>

    </div>

</nav>


<main class="container menu-page">

    <header class="section-heading">

        <div>

            <span class="eyebrow">ALMOST YOURS</span>

            <h1 class="page-title">
                Your cart
            </h1>

            <p>
                Select the items you want to purchase.
            </p>

        </div>

        <a class="text-link dark-link" href="menu.php">
            ← Keep browsing
        </a>

    </header>


    <?php if (!$items): ?>

        <div class="empty-cart">

            <span>☕</span>

            <h2>
                Your cart is waiting for something good.
            </h2>

            <p>
                Browse the menu and find your next favorite.
            </p>

            <a href="menu.php" class="btn">
                Explore the menu →
            </a>

        </div>

    <?php else: ?>

        <form action="update_cart.php" method="POST">

            <div class="select-all">

                <input
                    type="checkbox"
                    id="selectAll"
                    class="select-box"
                >

                <label for="selectAll">
                    Select All
                </label>

            </div>


            <div class="cart-table-wrap">

                <table class="cart-table">

                    <thead>

                        <tr>

                            <th></th>

                            <th>YOUR PICKS</th>

                            <th>PRICE</th>

                            <th>QTY</th>

                            <th>SUBTOTAL</th>

                            <th></th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php foreach ($items as $product): ?>

                        <tr>

                            <!-- CHECKBOX -->

                            <td>

                                <input
                                    type="checkbox"
                                    class="select-box product-check"
                                    name="selected[]"
                                    value="<?= (int)$product['id'] ?>"
                                    data-price="<?= (float)$product['price'] ?>"
                                    <?= $product['selected'] ? 'checked' : '' ?>
                                >

                            </td>


                            <!-- PRODUCT -->

                            <td>

                                <div class="cart-item">

                                    <?php
                                    $imagePath = __DIR__ . '/' . $product['image'];
                                    ?>

                                    <?php if (!empty($product['image']) && is_file($imagePath)): ?>

                                        <img
                                            class="cart-thumb"
                                            src="<?= htmlspecialchars($product['image']) ?>"
                                            alt="<?= htmlspecialchars($product['name']) ?>"
                                        >

                                    <?php else: ?>

                                        <div class="cart-thumb-placeholder">
                                            ☕
                                        </div>

                                    <?php endif; ?>


                                    <strong>
                                        <?= htmlspecialchars($product['name']) ?>
                                    </strong>

                                </div>

                            </td>


                            <!-- PRICE -->

                            <td>

                                ₱<?= number_format(
                                    (float)$product['price'],
                                    2
                                ) ?>

                            </td>


                            <!-- QUANTITY -->

                            <td>

                                <div class="quantity-control">

                                    <button
                                        type="button"
                                        class="quantity-btn minus"
                                        data-id="<?= (int)$product['id'] ?>"
                                    >
                                        −
                                    </button>


                                    <span
                                        class="quantity-number"
                                        id="qty-<?= (int)$product['id'] ?>"
                                    >
                                        <?= $product['quantity'] ?>
                                    </span>

                                    <input
                                        type="hidden"
                                        name="quantity[<?= (int)$product['id'] ?>]"
                                        id="quantity-input-<?= (int)$product['id'] ?>"
                                        value="<?= $product['quantity'] ?>"
                                    >


                                    <button
                                        type="button"
                                        class="quantity-btn plus"
                                        data-id="<?= (int)$product['id'] ?>"
                                    >
                                        +
                                    </button>

                                </div>

                            </td>


                            <!-- SUBTOTAL -->

                            <td>

                                <strong
                                    class="item-subtotal"
                                    id="subtotal-<?= (int)$product['id'] ?>"
                                    data-price="<?= (float)$product['price'] ?>"
                                >
                                    ₱<?= number_format(
                                        (float)$product['subtotal'],
                                        2
                                    ) ?>
                                </strong>

                            </td>


                            <!-- REMOVE -->

                            <td>

                                <a
                                    class="remove"
                                    href="remove_from_cart.php?id=<?= (int)$product['id'] ?>"
                                >
                                    Remove
                                </a>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>


            <div class="cart-bottom">

                <p>
                    Select the products you want to checkout.
                </p>


                <div class="cart-total">

                    <div class="selected-total">

                        <span>
                            Selected total
                        </span>

                        <strong id="selectedTotal">
                            ₱0.00
                        </strong>

                    </div>


                    <button
                        type="submit"
                        class="btn"
                        id="checkoutButton"
                    >
                        Continue to checkout →
                    </button>

                </div>

            </div>

        </form>

    <?php endif; ?>

</main>


<footer>

    <p>
        Made for slow mornings and good company · Brew Haven Coffee Shop
    </p>

</footer>


<script>

const checkboxes =
    document.querySelectorAll('.product-check');

const selectAll =
    document.getElementById('selectAll');

const selectedTotal =
    document.getElementById('selectedTotal');

const checkoutButton =
    document.getElementById('checkoutButton');


// -----------------------------
// CALCULATE SELECTED TOTAL
// -----------------------------

function calculateTotal() {

    let total = 0;

    checkboxes.forEach(function(checkbox) {

        if (checkbox.checked) {

            const id = checkbox.value;

            const qtyElement =
                document.getElementById('qty-' + id);

            const quantity =
                parseInt(qtyElement.textContent) || 1;

            const price =
                parseFloat(checkbox.dataset.price);

            total += price * quantity;
        }

    });


    selectedTotal.textContent =
        '₱' + total.toLocaleString(
            'en-PH',
            {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }
        );


    if (total > 0) {

        checkoutButton.classList.remove(
            'checkout-disabled'
        );

    } else {

        checkoutButton.classList.add(
            'checkout-disabled'
        );

    }
}


// -----------------------------
// SELECT ALL
// -----------------------------

if (selectAll) {

    selectAll.addEventListener(
        'change',
        function() {

            checkboxes.forEach(function(checkbox) {

                checkbox.checked =
                    selectAll.checked;

            });

            calculateTotal();

        }
    );

}


// -----------------------------
// INDIVIDUAL CHECKBOX
// -----------------------------

checkboxes.forEach(function(checkbox) {

    checkbox.addEventListener(
        'change',
        function() {

            const allChecked =
                [...checkboxes].every(
                    checkbox => checkbox.checked
                );

            selectAll.checked = allChecked;

            calculateTotal();

        }
    );

});


// -----------------------------
// QUANTITY BUTTONS
// -----------------------------

document.querySelectorAll('.quantity-btn')
.forEach(function(button) {

    button.addEventListener(
        'click',
        function() {

            const id =
                this.dataset.id;

            const qtyElement =
                document.getElementById(
                    'qty-' + id
                );

            let quantity =
                parseInt(
                    qtyElement.textContent
                ) || 1;


            if (this.classList.contains('plus')) {

                quantity++;

            }


            if (this.classList.contains('minus')) {

                if (quantity > 1) {

                    quantity--;

                }

            }


            qtyElement.textContent =
                quantity;

            document.getElementById(
                'quantity-input-' + id
            ).value = quantity;


            const price =
                parseFloat(
                    document.querySelector(
                        '.product-check[value="' + id + '"]'
                    ).dataset.price
                );


            const subtotal =
                price * quantity;


            document.getElementById(
                'subtotal-' + id
            ).textContent =
                '₱' + subtotal.toLocaleString(
                    'en-PH',
                    {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }
                );


            calculateTotal();

        }
    );

});


// Initial calculation
calculateTotal();

</script>

</body>

</html>
