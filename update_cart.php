<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: cart.php');
    exit;
}

$cart = $_SESSION['cart'] ?? [];
$postedQuantities = $_POST['quantity'] ?? [];
foreach ($cart as $id => $quantity) {
    if (isset($postedQuantities[$id])) {
        $cart[$id] = min(99, max(1, (int) $postedQuantities[$id]));
    }
}
$_SESSION['cart'] = $cart;

$postedSelection = $_POST['selected'] ?? [];
$validSelection = [];
if (is_array($postedSelection)) {
    foreach ($postedSelection as $id) {
        $id = (int) $id;
        if (isset($cart[$id])) {
            $validSelection[] = $id;
        }
    }
}

if (!$validSelection) {
    unset($_SESSION['selected_cart']);
    header('Location: cart.php?select=1');
    exit;
}

$_SESSION['selected_cart'] = array_values(array_unique($validSelection));
header('Location: checkout.php');
exit;
