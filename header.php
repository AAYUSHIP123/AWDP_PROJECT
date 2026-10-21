<?php
require_once __DIR__ . '/functions.php';
$userName    = $_SESSION['user_name'] ?? '';
$initial     = $userName ? strtoupper(substr(trim($userName), 0, 1)) : '';
$currentPage = basename($_SERVER['PHP_SELF']);
$cartCount   = cart_count();
if ($currentPage === 'register.php') {
  $bodyClass = 'account-register-page';
} elseif ($currentPage === 'login.php') {
  $bodyClass = 'account-login-page';
} elseif ($currentPage === 'forgot-password.php') {
  $bodyClass = 'account-login-page';
} elseif ($currentPage === 'index.php') {
  $bodyClass = 'home-page';
} elseif ($currentPage === 'products.php') {
  $bodyClass = 'shop-page';
} elseif ($currentPage === 'categories.php') {
  $bodyClass = 'categories-page';
} elseif ($currentPage === 'about.php') {
  $bodyClass = 'about-story-page';
} elseif ($currentPage === 'contact.php') {
  $bodyClass = 'contact-story-page';
} elseif ($currentPage === 'cart.php') {
  $bodyClass = 'cart-story-page';
} elseif ($currentPage === 'product-details.php') {
  $bodyClass = 'product-detail-story-page';
} elseif ($currentPage === 'orders.php') {
  $bodyClass = 'orders-story-page';
} elseif ($currentPage === 'profile.php') {
  $bodyClass = 'profile-story-page';
} elseif ($currentPage === 'order-details.php') {
  $bodyClass = 'order-details-story-page';
} elseif ($currentPage === 'checkout.php') {
  $bodyClass = 'checkout-story-page';
} elseif ($currentPage === 'success.php') {
  $bodyClass = 'success-story-page';
} else {
  $bodyClass = '';
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($title ?? 'The Wildflower Studio') ?></title>
  
  <!-- Bootstrap 5.3 CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <!-- Custom Site Stylesheet -->
  <link rel="stylesheet" href="<?= e($base ?? '') ?>css/style.css?v=<?= filemtime(__DIR__ . '/../css/style.css') ?>">
</head>
<body class="<?= e($bodyClass) ?>">

<!-- TOP ANNOUNCEMENT BAR (#E8C9C5 Blush Pink + #293B30 Dark Green) -->
<div class="site-topbar">
  “A little beauty, delivered with love ✿”
</div>

<!-- MAIN STICKY HEADER (#FAF7F1 Warm Cream + #293B30 Dark Green) -->
<header class="site-header sticky-top">
  <nav class="navbar navbar-expand-lg site-navbar p-0">
    <div class="container-fluid site-navbar-container">
      
      <!-- LEFT: LOGO -->
      <a class="navbar-brand header-logo py-0 me-0" href="<?= e($base ?? '') ?>index.php">
        <img src="<?= e($base ?? '') ?>images/WildflowerStudio.png" alt="Wildflower Studio" class="site-logo-img">
      </a>

      <!-- MOBILE ACTIONS (Right side on Mobile: Cart + Menu Hamburger) -->
      <div class="d-flex align-items-center gap-2 d-lg-none mobile-header-actions">
        
        <!-- Mobile Cart Icon + Count -->
        <a class="header-pill mobile-action-pill mobile-cart-pill" href="<?= e($base ?? '') ?>cart.php" title="Cart">
          <span class="cart-icon-wrapper">
            <i class="bi bi-cart2 fs-5"></i>
            <span class="cart-badge-floating"><?= $cartCount ?></span>
          </span>
        </a>

        <!-- Mobile Menu Hamburger Toggler -->
        <button class="header-pill mobile-action-pill mobile-menu-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#siteNavbarCollapse" aria-controls="siteNavbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
          <i class="bi bi-list fs-4"></i>
        </button>

      </div>

      <!-- COLLAPSIBLE NAVBAR CONTENT (Center links + Desktop right actions + Mobile drop links) -->
      <div class="collapse navbar-collapse" id="siteNavbarCollapse">
        
        <!-- MOBILE CLOSE BUTTON (Visible only on mobile inside fullscreen menu) -->
        <div class="d-flex d-lg-none justify-content-between align-items-center w-100 mobile-menu-header mb-4 px-3">
          <img src="<?= e($base ?? '') ?>images/WildflowerStudio.png" alt="Wildflower Studio" style="max-height: 44px; width: auto;">
          <button class="mobile-close-btn border-0 bg-transparent p-2" type="button" data-bs-toggle="collapse" data-bs-target="#siteNavbarCollapse" aria-label="Close menu">
            <i class="bi bi-x-lg fs-3 text-dark"></i>
          </button>
        </div>

        <!-- CENTER: NAVIGATION LINKS -->
        <ul class="navbar-nav mx-auto header-nav-links my-2 my-lg-0 text-center">
          <li class="nav-item">
            <a class="nav-link <?= $currentPage === 'index.php' ? 'active' : '' ?>" href="<?= e($base ?? '') ?>index.php">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= $currentPage === 'products.php' ? 'active' : '' ?>" href="<?= e($base ?? '') ?>products.php">Shop</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= $currentPage === 'categories.php' ? 'active' : '' ?>" href="<?= e($base ?? '') ?>categories.php">Collections</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= $currentPage === 'about.php' ? 'active' : '' ?>" href="<?= e($base ?? '') ?>about.php">Our Story</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= $currentPage === 'contact.php' ? 'active' : '' ?>" href="<?= e($base ?? '') ?>contact.php">Contact</a>
          </li>
        </ul>

        <!-- RIGHT: DESKTOP CART & ACCOUNT PILLS (Visible on >= 992px) -->
        <div class="d-none d-lg-flex align-items-center gap-3 desktop-header-actions">
          
          <!-- Desktop Cart Pill -->
          <a class="header-pill cart-pill" href="<?= e($base ?? '') ?>cart.php" title="Shopping Cart">
            <span class="cart-icon-wrapper">
              <i class="bi bi-cart2 pill-icon"></i>
              <span class="cart-badge-floating"><?= $cartCount ?></span>
            </span>
            <span class="pill-text ms-1">Cart</span>
          </a>

          <!-- Desktop User / Account Pill -->
          <?php if (!empty($_SESSION['user_id'])): ?>
            <div class="dropdown header-user-dropdown">
              <button class="header-pill user-pill dropdown-toggle" type="button" id="headerUserDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="user-avatar-circle"><?= $initial ?></span>
                <span class="pill-text user-name-text"><?= e($userName) ?></span>
                <span class="dropdown-arrow-icon">▾</span>
              </button>
              <ul class="dropdown-menu dropdown-menu-end site-dropdown-menu shadow-sm" aria-labelledby="headerUserDropdown">
                <li class="dropdown-header">
                  Signed in as<br><strong><?= e($userName) ?></strong>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="<?= e($base ?? '') ?>orders.php"><i class="bi bi-box-seam me-2"></i>My Orders</a></li>
                <li><a class="dropdown-item" href="<?= e($base ?? '') ?>profile.php"><i class="bi bi-person-gear me-2"></i>Profile</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="<?= e($base ?? '') ?>logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
              </ul>
            </div>
          <?php else: ?>
            <a class="header-pill user-pill" href="<?= e($base ?? '') ?>login.php" title="Account">
              <i class="bi bi-person pill-icon fs-5"></i>
              <span class="pill-text">Account</span>
            </a>
          <?php endif; ?>

        </div>

        <!-- MOBILE-ONLY EXTRA LINKS (Inside Collapse when expanded) -->
        <div class="d-lg-none mobile-drawer-extra pt-3 pb-2 border-top">
          <?php if (!empty($_SESSION['user_id'])): ?>
            <div class="px-3 py-2 text-muted small">
              Signed in as <strong><?= e($userName) ?></strong>
            </div>
            <a class="nav-link mobile-drawer-link" href="<?= e($base ?? '') ?>orders.php">
              <i class="bi bi-box-seam me-2"></i>My Orders
            </a>
            <a class="nav-link mobile-drawer-link" href="<?= e($base ?? '') ?>profile.php">
              <i class="bi bi-person-gear me-2"></i>Profile
            </a>
            <a class="nav-link mobile-drawer-link text-danger" href="<?= e($base ?? '') ?>logout.php">
              <i class="bi bi-box-arrow-right me-2"></i>Logout
            </a>
          <?php else: ?>
            <a class="nav-link mobile-drawer-link" href="<?= e($base ?? '') ?>login.php">
              <i class="bi bi-box-arrow-in-right me-2"></i>Sign In / Account
            </a>
            <a class="nav-link mobile-drawer-link" href="<?= e($base ?? '') ?>register.php">
              <i class="bi bi-person-plus me-2"></i>Create New Account
            </a>
          <?php endif; ?>
        </div>

      </div>

    </div>
  </nav>
</header>

<?php if (basename($_SERVER['PHP_SELF']) !== 'index.php'): ?>
<div class="page">
<?php endif; ?>
