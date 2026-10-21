<?php
require 'config/database.php';
require 'includes/functions.php';
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

// Add to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $id  = (int)($_POST['product_id'] ?? 0);
    $qty = max(1, (int)($_POST['quantity'] ?? 1));
    $st  = $conn->prepare("SELECT stock FROM products WHERE id=? AND status='active'");
    $st->bind_param('i', $id);
    $st->execute();
    $p = $st->get_result()->fetch_assoc();
    if ($p && $p['stock'] > 0) $_SESSION['cart'][$id] = min($qty, (int)$p['stock']);
    redirect('cart.php');
}

// Remove item
if (isset($_GET['remove'])) {
    unset($_SESSION['cart'][(int)$_GET['remove']]);
    redirect('cart.php');
}

// Update quantities
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $stockStmt = $conn->prepare("SELECT stock FROM products WHERE id=? AND status='active'");
    foreach ($_POST['qty'] ?? [] as $id => $q) {
        $id = (int)$id;
        $qty = (int)$q;
        if ($id < 1 || !isset($_SESSION['cart'][$id])) continue;

        $stockStmt->bind_param('i', $id);
        $stockStmt->execute();
        $product = $stockStmt->get_result()->fetch_assoc();

        if (!$product || $qty < 1 || (int)$product['stock'] < 1) {
            unset($_SESSION['cart'][$id]);
        } else {
            $_SESSION['cart'][$id] = min($qty, (int)$product['stock']);
        }
    }
    $stockStmt->close();
    redirect('cart.php');
}

$items = cart_items($conn);
$total = cart_total($items);
$title = 'My Cart';
require 'includes/header.php';
?>

<main class="cart-page">
  <header class="cart-page-header">
    <span class="cart-page-label">YOUR SELECTION</span>
    <h1>Your Flower Basket</h1>
    <p>A little beauty is on its way. Review your favourite blooms before checkout.</p>
  </header>

<?php if (!$items): ?>
  <section class="empty-state cart-empty-state">
    <span class="cart-empty-flower" aria-hidden="true"><i class="bi bi-flower1"></i></span>
    <span class="cart-page-label">A FRESH START</span>
    <h2>Your Basket Is Waiting to Bloom.</h2>
    <p>Your cart is looking a little empty. Find a bouquet that makes your heart bloom.</p>
    <a class="cart-empty-button" href="products.php">Explore Our Flowers <i class="bi bi-flower3" aria-hidden="true"></i></a>
  </section>
<?php else: ?>

<div class="cart-grid">
  <!-- CART TABLE -->
  <form method="post" class="cart-items-form">
    <input type="hidden" name="update" value="1">
    <div class="cart-table-wrap">
      <table class="data-table">
        <thead>
          <tr>
            <th>Product</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Subtotal</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($items as $p): ?>
          <tr>
            <td>
              <strong><?= e($p['name']) ?></strong>
            </td>
            <td><?= price($p['price']) ?></td>
            <td>
              <label class="cart-quantity-control">
                <span class="visually-hidden">Quantity for <?= e($p['name']) ?></span>
                <input type="number" name="qty[<?= $p['id'] ?>]"
                     min="1" max="<?= $p['stock'] ?>" value="<?= $p['qty'] ?>"
                     aria-label="Quantity for <?= e($p['name']) ?>">
                <i class="bi bi-arrow-repeat" aria-hidden="true"></i>
              </label>
            </td>
            <td><strong><?= price($p['price'] * $p['qty']) ?></strong></td>
            <td>
              <a href="cart.php?remove=<?= $p['id'] ?>" class="btn btn-danger btn-sm">Remove</a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </form>

  <!-- ORDER SUMMARY -->
  <div class="cart-summary">
    <h3>Order Summary</h3>
    <?php foreach ($items as $p): ?>
    <div class="summary-row">
      <span><?= e($p['name']) ?> × <?= $p['qty'] ?></span>
      <span><?= price($p['price'] * $p['qty']) ?></span>
    </div>
    <?php endforeach; ?>
    <div class="summary-row">
      <span>Shipping</span>
      <span style="color:var(--success);font-weight:600">
        <?= $total >= 999 ? 'Free' : '₹49' ?>
      </span>
    </div>
    <div class="summary-total">
      <span>Total</span>
      <span><?= price($total >= 999 ? $total : $total + 49) ?></span>
    </div>
    <a href="checkout.php" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:18px;font-size:15px">
      Proceed to Checkout →
    </a>
    <div style="text-align:center;margin-top:12px;font-size:12px;color:var(--muted)">
      🔒 Secure checkout
    </div>
  </div>
</div>

<?php endif; ?>
</main>

<?php if ($items): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
  var cartForm = document.querySelector('.cart-items-form');
  if (!cartForm) return;

  cartForm.querySelectorAll('input[name^="qty["]').forEach(function (quantityInput) {
    quantityInput.addEventListener('change', function () {
      cartForm.requestSubmit();
    });
  });
});
</script>
<?php endif; ?>

<?php require 'includes/footer.php'; ?>
