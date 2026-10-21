<?php
$title = 'My Wishlist';
require 'includes/header.php';
?>

<div class="page-header">
  <div>
    <h1>My Wishlist</h1>
    <p>Saved items for later purchase</p>
  </div>
</div>

<div class="empty-state">
  <div class="icon">💖</div>
  <h2>Your Wishlist is Empty</h2>
  <p>Explore our latest creations and add your favorite handmade items.</p>
  <a class="btn btn-primary" href="products.php">Explore Shop</a>
</div>

<?php require 'includes/footer.php'; ?>