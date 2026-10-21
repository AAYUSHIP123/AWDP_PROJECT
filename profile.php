<?php
require 'config/database.php';
require 'includes/functions.php';
user_required();

$uid     = (int)$_SESSION['user_id'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $mobile  = trim($_POST['mobile'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $city    = trim($_POST['city'] ?? '');
    $state   = trim($_POST['state'] ?? '');
    $pincode = trim($_POST['pincode'] ?? '');

    if (!empty($name)) {
        $st = $conn->prepare('UPDATE users SET name=?, mobile=?, address=?, city=?, state=?, pincode=? WHERE id=?');
        $st->bind_param('ssssssi', $name, $mobile, $address, $city, $state, $pincode, $uid);
        $st->execute();
        $_SESSION['user_name'] = $name;
        $message = 'Your profile details have been saved successfully.';
    } else {
        $message = 'Name is required.';
    }
}

$u = $conn->query("SELECT * FROM users WHERE id=$uid")->fetch_assoc();
$orderCountRes = $conn->query("SELECT COUNT(*) AS total FROM orders WHERE user_id=$uid");
$orderCount = $orderCountRes ? $orderCountRes->fetch_assoc()['total'] : 0;

$title = 'My Profile';
require 'includes/header.php';
?>

<main class="profile-page">
  <header class="profile-intro">
    <div>
      <span class="account-page-eyebrow">YOUR LITTLE CORNER</span>
      <h1>My Profile</h1>
      <p>Keep your details and delivery address ready for the next special moment.</p>
    </div>
    <a href="orders.php" class="profile-orders-link"><i class="bi bi-box-seam"></i> View My Orders <span><?= (int)$orderCount ?></span></a>
  </header>

  <?php if ($message): ?>
    <div class="alert alert-success profile-message"><i class="bi bi-check-circle-fill"></i><?= e($message) ?></div>
  <?php endif; ?>

  <div class="profile-layout">
    <aside class="profile-summary-card">
      <div class="profile-avatar"><?= e(strtoupper(substr($u['name'] ?? 'U', 0, 1))) ?></div>
      <span class="profile-member-label">WILDFLOWER CUSTOMER</span>
      <h2><?= e($u['name']) ?></h2>
      <p class="profile-summary-email"><i class="bi bi-envelope"></i><?= e($u['email']) ?></p>
      <div class="profile-summary-divider"></div>
      <div class="profile-summary-stat">
        <span class="profile-stat-icon"><i class="bi bi-box2-heart"></i></span>
        <span><strong><?= (int)$orderCount ?></strong><small><?= (int)$orderCount === 1 ? 'flower order' : 'flower orders' ?></small></span>
      </div>
      <p class="profile-summary-note">Your account helps keep every thoughtful delivery a little more effortless.</p>
      <a href="logout.php" class="profile-logout-link"><i class="bi bi-box-arrow-right"></i> Log Out</a>
    </aside>

    <section class="profile-edit-card">
      <div class="profile-edit-heading">
        <span class="account-page-eyebrow">ACCOUNT DETAILS</span>
        <h2>Make it yours</h2>
        <p>Update your contact information and where you would like your flowers delivered.</p>
      </div>

      <form method="post" class="profile-edit-form">
        <div class="form-grid">
          <div class="form-group">
            <label for="profileName">Full Name <span>*</span></label>
            <input id="profileName" name="name" value="<?= e($u['name']) ?>" required>
          </div>
          <div class="form-group">
            <label for="profileEmail">Email Address <span class="profile-readonly-tag">READ ONLY</span></label>
            <input id="profileEmail" value="<?= e($u['email']) ?>" disabled>
          </div>
          <div class="form-group full">
            <label for="profileMobile">Mobile Number</label>
            <input id="profileMobile" name="mobile" value="<?= e($u['mobile']) ?>" placeholder="10-digit contact number">
          </div>
          <div class="form-group full">
            <label for="profileAddress">Default Shipping Address</label>
            <textarea id="profileAddress" name="address" rows="3" placeholder="Enter house no, street name, area"><?= e($u['address']) ?></textarea>
          </div>
          <div class="form-group">
            <label for="profileCity">City</label>
            <input id="profileCity" name="city" value="<?= e($u['city']) ?>">
          </div>
          <div class="form-group">
            <label for="profileState">State</label>
            <input id="profileState" name="state" value="<?= e($u['state']) ?>">
          </div>
          <div class="form-group full">
            <label for="profilePincode">Pincode / Postal Code</label>
            <input id="profilePincode" name="pincode" value="<?= e($u['pincode']) ?>">
          </div>
        </div>
        <div class="profile-form-actions">
          <button type="submit" class="profile-save-button"><i class="bi bi-check2"></i> Save Profile Changes</button>
        </div>
      </form>
    </section>
  </div>
</main>

<?php require 'includes/footer.php'; ?>
