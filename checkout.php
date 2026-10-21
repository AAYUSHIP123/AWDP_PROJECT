<?php
require 'config/database.php';
require 'includes/functions.php';
user_required();

$items = cart_items($conn);
$total = cart_total($items);
if (!$items) redirect('cart.php');

$u = $conn->query('SELECT * FROM users WHERE id=' . (int)$_SESSION['user_id'])->fetch_assoc();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $mobile   = trim($_POST['mobile'] ?? '');
    $billing  = trim($_POST['billing_address'] ?? '');
    $shipping = trim($_POST['shipping_address'] ?? '');
    $city     = trim($_POST['city'] ?? '');
    $state    = trim($_POST['state'] ?? '');
    $pincode  = trim($_POST['pincode'] ?? '');
    $method   = $_POST['payment_method'] ?? 'Cash on Delivery';

    if (empty($name) || empty($email) || empty($mobile) || empty($shipping) || empty($city) || empty($state) || empty($pincode)) {
        $message = 'Please fill in all required shipping and contact fields.';
    } else {
        $conn->begin_transaction();
        try {
            $st = $conn->prepare('INSERT INTO orders(user_id,name,email,mobile,billing_address,shipping_address,city,state,pincode,total,payment_method,payment_status,order_status) VALUES(?,?,?,?,?,?,?,?,?,?,?,"Pending","Confirmed")');
            $uid = (int)$u['id'];
            $st->bind_param('issssssssds', $uid, $name, $email, $mobile, $billing, $shipping, $city, $state, $pincode, $total, $method);
            $st->execute();
            $oid = $st->insert_id;

            $it = $conn->prepare('INSERT INTO order_items(order_id,product_id,name,price,quantity) VALUES(?,?,?,?,?)');

            foreach ($items as $p) {
                $it->bind_param('iisdi', $oid, $p['id'], $p['name'], $p['price'], $p['qty']);
                $it->execute();
            }

            // Also update user profile with latest address for convenience
            $upUser = $conn->prepare('UPDATE users SET mobile=?, address=?, city=?, state=?, pincode=? WHERE id=?');
            $upUser->bind_param('sssssi', $mobile, $shipping, $city, $state, $pincode, $uid);
            $upUser->execute();

            $_SESSION['cart'] = [];
            $conn->commit();
            redirect('success.php?id=' . $oid);
        } catch (Exception $e) {
            $conn->rollback();
            $message = 'Could not place order: ' . $e->getMessage();
        }
    }
}

$title = 'Checkout';
require 'includes/header.php';
?>

<main class="checkout-page">
  <a href="cart.php" class="checkout-back-link"><i class="bi bi-arrow-left"></i> Back to Your Basket</a>
  <header class="checkout-intro">
    <span class="account-page-eyebrow">THE FINAL, LOVELY STEP</span>
    <h1>Complete Your Order</h1>
    <p>A few details, then we'll get your blooms ready with care.</p>
  </header>

  <?php if ($message): ?>
    <div class="alert alert-danger checkout-error"><i class="bi bi-exclamation-circle-fill"></i><?= e($message) ?></div>
  <?php endif; ?>

  <form method="post" class="checkout-layout">
    <div class="checkout-main">
      <section class="checkout-card">
        <div class="checkout-section-heading">
          <span class="checkout-step-number">01</span>
          <div>
            <span class="account-page-eyebrow">WHERE SHOULD WE SEND IT?</span>
            <h2>Shipping &amp; Contact Details</h2>
          </div>
        </div>

        <div class="form-grid checkout-fields">
          <div class="form-group">
            <label for="checkoutName">Full Name <span>*</span></label>
            <input id="checkoutName" name="name" value="<?= e($u['name'] ?? '') ?>" required>
          </div>
          <div class="form-group">
            <label for="checkoutMobile">Mobile Number <span>*</span></label>
            <input id="checkoutMobile" name="mobile" value="<?= e($u['mobile'] ?? '') ?>" required>
          </div>
          <div class="form-group full">
            <label for="checkoutEmail">Email Address <span>*</span></label>
            <input id="checkoutEmail" type="email" name="email" value="<?= e($u['email'] ?? '') ?>" required>
          </div>
          <div class="form-group full">
            <label for="checkoutAddress">Shipping Delivery Address <span>*</span></label>
            <textarea id="checkoutAddress" name="shipping_address" rows="3" placeholder="House/Flat No., Street, Landmark" required><?= e($u['address'] ?? '') ?></textarea>
          </div>
          <div class="form-group full">
            <label for="checkoutBilling">Billing Address <small>Optional — leave empty if same</small></label>
            <textarea id="checkoutBilling" name="billing_address" rows="2" placeholder="Same as shipping address if empty"><?= e($u['address'] ?? '') ?></textarea>
          </div>
          <div class="form-group">
            <label for="checkoutCity">City <span>*</span></label>
            <input id="checkoutCity" name="city" value="<?= e($u['city'] ?? '') ?>" required>
          </div>
          <div class="form-group">
            <label for="checkoutState">State <span>*</span></label>
            <input id="checkoutState" name="state" value="<?= e($u['state'] ?? '') ?>" required>
          </div>
          <div class="form-group">
            <label for="checkoutPincode">Pincode / Postal Code <span>*</span></label>
            <input id="checkoutPincode" name="pincode" value="<?= e($u['pincode'] ?? '') ?>" required>
          </div>
        </div>
      </section>

      <section class="checkout-card checkout-payment-card">
        <div class="checkout-section-heading">
          <span class="checkout-step-number">02</span>
          <div>
            <span class="account-page-eyebrow">HOW WOULD YOU LIKE TO PAY?</span>
            <h2>Payment Option</h2>
          </div>
        </div>

        <div class="checkout-payment-options">
          <label class="checkout-payment-option">
            <input type="radio" name="payment_method" value="Cash on Delivery" checked>
            <span class="checkout-payment-icon"><i class="bi bi-cash-coin"></i></span>
            <span class="checkout-payment-copy">
              <strong>Cash on Delivery (COD)</strong>
              <small>Pay in cash when your order arrives</small>
            </span>
            <i class="bi bi-check-circle-fill checkout-payment-check"></i>
          </label>
          <label class="checkout-payment-option">
            <input type="radio" name="payment_method" value="UPI">
            <span class="checkout-payment-icon"><i class="bi bi-phone"></i></span>
            <span class="checkout-payment-copy">
              <strong>UPI / QR Code</strong>
              <small>Google Pay, PhonePe, Paytm (Instant Demo)</small>
            </span>
            <i class="bi bi-check-circle-fill checkout-payment-check"></i>
          </label>
          <label class="checkout-payment-option">
            <input type="radio" name="payment_method" value="Card">
            <span class="checkout-payment-icon"><i class="bi bi-credit-card"></i></span>
            <span class="checkout-payment-copy">
              <strong>Debit / Credit Card</strong>
              <small>Visa, MasterCard, RuPay (Demo)</small>
            </span>
            <i class="bi bi-check-circle-fill checkout-payment-check"></i>
          </label>
        </div>
      </section>
    </div>

    <aside class="checkout-summary">
      <div class="checkout-summary-heading">
        <span class="account-page-eyebrow">A QUICK LOOK</span>
        <h2>Your Order</h2>
      </div>
      <div class="checkout-order-items">
        <?php foreach ($items as $p): ?>
          <div class="checkout-order-item">
            <div>
              <strong><?= e($p['name']) ?></strong>
              <small>Qty: <?= (int)$p['qty'] ?> × <?= price($p['price']) ?></small>
            </div>
            <strong class="checkout-line-total"><?= price($p['price'] * $p['qty']) ?></strong>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="checkout-summary-row"><span>Subtotal</span><strong><?= price($total) ?></strong></div>
      <div class="checkout-summary-row"><span>Shipping</span><strong class="checkout-free-shipping"><i class="bi bi-flower1"></i> Free Delivery</strong></div>
      <div class="checkout-grand-total"><span>Total Payable</span><strong><?= price($total) ?></strong></div>

      <button type="submit" class="checkout-place-order"><i class="bi bi-check2-circle"></i> Place Order Now</button>
      <div class="checkout-secure-note"><i class="bi bi-shield-lock"></i> 100% Safe &amp; Secure Checkout</div>
    </aside>
  </form>
</main>

<?php require 'includes/footer.php'; ?>
