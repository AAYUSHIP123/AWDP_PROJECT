<?php
require 'config/database.php';
require 'includes/functions.php';
user_required();

$id = (int)($_GET['id'] ?? 0);
$st = $conn->prepare('SELECT * FROM orders WHERE id=? AND user_id=?');
$st->bind_param('ii', $id, $_SESSION['user_id']);
$st->execute();
$order = $st->get_result()->fetch_assoc();
if (!$order) {
    redirect('orders.php');
}

$title = 'Order Confirmed!';
require 'includes/header.php';
?>

<main class="success-page">
  <section class="success-card">
    <div class="success-flower-badge"><i class="bi bi-flower1"></i><span><i class="bi bi-check-lg"></i></span></div>
    <span class="account-page-eyebrow">A LITTLE BEAUTY IS ON ITS WAY</span>
    <h1>Thank You for Your Order!</h1>
    <p class="success-intro">Your order is in, and our team is getting your blooms ready with love.</p>

    <div class="success-order-number">
      <span>ORDER NUMBER</span>
      <strong>#<?= (int)$order['id'] ?></strong>
    </div>

    <div class="success-order-details">
      <div><span><i class="bi bi-calendar-heart"></i> Date &amp; Time</span><strong><?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></strong></div>
      <div><span><i class="bi bi-credit-card"></i> Payment Method</span><strong><?= e($order['payment_method']) ?></strong></div>
      <div class="success-total-row"><span>Total Amount</span><strong><?= price($order['total']) ?></strong></div>
    </div>

    <div class="success-actions">
      <a class="success-primary-action" href="order-details.php?id=<?= (int)$order['id'] ?>"><i class="bi bi-box-seam"></i> View Order Details</a>
      <a class="success-secondary-action" href="products.php"><i class="bi bi-flower1"></i> Continue Shopping</a>
    </div>
    <p class="success-signoff"><i class="bi bi-heart-fill"></i> Handcrafted with love, just for you.</p>
  </section>
</main>

<?php require 'includes/footer.php'; ?>
