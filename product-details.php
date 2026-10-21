<?php
require 'config/database.php';
$id = (int)($_GET['id'] ?? 0);
$st = $conn->prepare("SELECT p.*, c.name category_name FROM products p LEFT JOIN categories c ON p.category_id=c.id WHERE p.id=? AND p.status='active'");
$st->bind_param('i', $id);
$st->execute();
$product = $st->get_result()->fetch_assoc();
if (!$product) die('Product not found.');
$title = $product['name'];
require 'includes/header.php';
?>

  <main class="product-detail-page">
    <a class="detail-back-link" href="products.php"><i class="bi bi-arrow-left"></i> Back to all flowers</a>

    <div class="detail-grid">
      <div class="detail-img">
        <?php if ($product['image']): ?>
          <img src="<?= product_image_url($product['image']) ?>" alt="<?= e($product['name']) ?>">
        <?php else: ?>
          <span class="detail-image-placeholder" aria-hidden="true"><i class="bi bi-flower1"></i></span>
        <?php endif; ?>
        <?php if (!empty($product['discount']) && $product['discount'] > 0): ?>
          <span class="home-discount-badge">SAVE <?= price($product['discount']) ?></span>
        <?php endif; ?>
        <span class="detail-image-note"><i class="bi bi-flower2"></i> A little beauty, just for you</span>
      </div>

      <section class="detail-info" aria-labelledby="product-title">
        <span class="detail-kicker">THE WILDFLOWER EDIT</span>
        <span class="category-tag"><?= e($product['category_name'] ?? $product['category'] ?? 'Handmade') ?></span>
        <h1 id="product-title"><?= e($product['name']) ?></h1>
        <div class="detail-price">
          <?= price($product['price']) ?>
          <?php if (!empty($product['discount']) && $product['discount'] > 0): ?>
            <span class="detail-original-price"><?= price($product['price'] + $product['discount']) ?></span>
          <?php endif; ?>
        </div>

        <?php if ($product['stock'] > 0): ?>
          <span class="stock-badge in"><i class="bi bi-check-circle-fill"></i> In Stock <span>(<?= $product['stock'] ?> left)</span></span>
        <?php else: ?>
          <span class="stock-badge out"><i class="bi bi-x-circle-fill"></i> Out of Stock</span>
        <?php endif; ?>

        <p class="desc"><?= nl2br(e($product['description'])) ?></p>

        <?php if ($product['stock'] > 0): ?>
        <form action="cart.php" method="post" class="detail-buy-form">
          <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
          <label for="detail-quantity">Choose quantity</label>
          <div class="qty-row">
            <input id="detail-quantity" class="qty-input" type="number" name="quantity" value="1" min="1" max="<?= $product['stock'] ?>">
            <button type="submit" class="btn btn-primary"><i class="bi bi-basket2"></i> Add to Cart</button>
          </div>
        </form>
        <?php else: ?>
          <div class="detail-stock-notice"><i class="bi bi-heart"></i> This flower is resting for now. Please check back soon.</div>
        <?php endif; ?>
      </section>
    </div>
  </div>
  </main>
</div>

<?php require 'includes/footer.php'; ?>
