<?php
session_start();
$cartCount = array_sum($_SESSION['cart'] ?? []);
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Brew Haven | Coffee for your kind of day</title><link rel="stylesheet" href="style.css"></head>
<body>
<nav class="navbar"><a class="logo" href="index.php">☕ Brew Haven</a><div class="nav-links"><a class="active" href="index.php">Home</a><a href="menu.php">Menu</a><a href="cart.php">Cart <span class="nav-count"><?= $cartCount ?></span></a><a href="admin_index.php">Admin</a></div></nav>
<main>
    <section class="hero"><div class="hero-content"><span class="eyebrow">YOUR NEIGHBORHOOD COFFEE CORNER</span><h1>Make room for<br><em>a little joy.</em></h1><p>Thoughtfully brewed coffee, familiar favorites, and a cozy moment just for you.</p><div class="hero-actions"><a href="menu.php" class="btn">Explore the menu <span>→</span></a><a href="#our-story" class="text-link">A little about us ↓</a></div><div class="hero-note"><span>✦</span> Freshly made. Always welcoming.</div></div><div class="hero-art" aria-hidden="true"><div class="sun-disc"></div><div class="hero-beans">✳ &nbsp; ✳<br><br>&nbsp; ✳</div><div class="hero-cup"><div class="cup-steam">〰 &nbsp; 〰</div><div class="cup-saucer"></div><div class="cup-body"><span>BH</span></div><div class="cup-handle"></div></div><div class="art-caption">A GOOD CUP<br>CHANGES THE DAY</div></div></section>
    <section class="home-intro" id="our-story"><span class="eyebrow">A WARM WELCOME</span><h2>Good coffee. <em>Good company.</em></h2><p>We believe the best days begin with a small pause and something delicious. Find your usual, try something new, and let us make your day a little warmer.</p><a class="text-link dark-link" href="menu.php">Find your favorite <span>→</span></a></section>
    <section class="home-cta"><div><span class="eyebrow">READY WHEN YOU ARE</span><h2>Your next favorite is waiting.</h2></div><a href="menu.php" class="btn btn-light">See what’s brewing <span>→</span></a></section>
</main>
<footer><p>Made for slow mornings and good company · Brew Haven Coffee Shop</p></footer>
</body>
</html>
