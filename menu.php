<?php
session_start();
require_once __DIR__ . '/database.php';
$columns = $pdo->query("SHOW COLUMNS FROM products LIKE 'image'")->fetch();
if (!$columns) {
    $pdo->exec('ALTER TABLE products ADD COLUMN image VARCHAR(255) NULL');
}
$products = $pdo->query('SELECT * FROM products ORDER BY id DESC')->fetchAll();
$cartCount = array_sum($_SESSION['cart'] ?? []);
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Our Menu | Brew Haven</title><link rel="stylesheet" href="style.css"></head>
<body>
<nav class="navbar"><a class="logo" href="index.php">☕ Brew Haven</a><div class="nav-links"><a href="index.php">Home</a><a class="active" href="menu.php">Menu</a><a href="cart.php">Cart <span class="nav-count"><?= $cartCount ?></span></a><a href="admin_index.php">Admin</a></div></nav>
<main class="container menu-page">
    <header class="menu-intro"><span class="eyebrow">BREWED WITH CARE</span><h1>Find your new<br><em>favorite cup.</em></h1><p>Comfort in every pour. Pick something lovely and we’ll get it ready for you.</p></header>
    <?php if (isset($_GET['added'])): ?><div class="notice">Added to your cart. <a href="cart.php">View cart →</a></div><?php endif; ?>
    <?php if (!$products): ?><section class="empty-state menu-empty"><span>☕</span><h2>Fresh brews are on the way</h2><p>There are no products on the menu yet.</p></section><?php else: ?>
    <div class="products">
        <?php foreach ($products as $product): ?><article class="product-card">
            <?php if (!empty($product['image']) && is_file(__DIR__ . '/' . $product['image'])): ?><img class="product-image" src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>"><?php else: ?><div class="image-placeholder"><span>☕</span></div><?php endif; ?>
            <div class="product-copy"><span class="product-tag">BREW HAVEN FAVORITE</span><h2><?= htmlspecialchars($product['name']) ?></h2><p><?= htmlspecialchars($product['description']) ?></p><div class="product-buy"><strong>₱<?= number_format((float) $product['price'], 2) ?></strong><form action="add_to_cart.php" method="POST"><input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>"><button type="submit" class="btn">Add to cart <span>+</span></button></form></div></div>
        </article><?php endforeach; ?>
    </div><?php endif; ?>
</main>
<footer><p>Made for slow mornings and good company · Brew Haven Coffee Shop</p></footer>
</body>
</html>
