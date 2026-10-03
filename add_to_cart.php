<?php

session_start();

require_once __DIR__ . "/database.php";

if (!isset($_POST['product_id'])) {
    header("Location: menu.php");
    exit;
}

$product_id = intval($_POST['product_id']);

$stmt = $pdo->prepare("SELECT id FROM products WHERE id = ?");
$stmt->execute([$product_id]);

if (!$stmt->fetch()) {
    header("Location: menu.php");
    exit;
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_SESSION['cart'][$product_id])) {

    $_SESSION['cart'][$product_id]++;

} else {

    $_SESSION['cart'][$product_id] = 1;

}

header("Location: menu.php?added=1");
exit;

?>
