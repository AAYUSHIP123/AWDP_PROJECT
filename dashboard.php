<?php
$pageTitle  = 'Dashboard';
$activePage = 'dashboard';
require '../config/database.php';
require 'layout-header.php';

// Stats
$stats = [];
foreach (['users','categories','products','orders'] as $t)
    $stats[$t] = $conn->query("SELECT COUNT(*) n FROM $t")->fetch_assoc()['n'];
$sales = $conn->query("SELECT COALESCE(SUM(total),0) n FROM orders WHERE order_status = 'Delivered'")->fetch_assoc()['n'];

// Recent orders
$recentOrders = $conn->query("SELECT * FROM orders ORDER BY id DESC LIMIT 7");
?>

<!-- STAT CARDS -->
<div class="stat-grid">
  <div class="stat-card">
    <div class="stat-icon purple">👥</div>
    <div class="stat-info">
      <div class="stat-value"><?= $stats['users'] ?></div>
      <div class="stat-label">Total Users</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon blue">📦</div>
    <div class="stat-info">
      <div class="stat-value"><?= $stats['products'] ?></div>
      <div class="stat-label">Total Products</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon orange">🧾</div>
    <div class="stat-info">
      <div class="stat-value"><?= $stats['orders'] ?></div>
      <div class="stat-label">Total Orders</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon green">🗂️</div>
    <div class="stat-info">
      <div class="stat-value"><?= $stats['categories'] ?></div>
      <div class="stat-label">Categories</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon pink">💰</div>
    <div class="stat-info">
      <div class="stat-value" style="font-size:20px"><?= price($sales) ?></div>
      <div class="stat-label">Total Revenue</div>
    </div>
  </div>
</div>

<!-- QUICK ACTIONS -->
<div class="quick-actions">
  <a href="add-product.php" class="btn btn-primary">➕ Add Product</a>
  <a href="categories.php"  class="btn btn-outline">🗂️ Manage Categories</a>
  <a href="orders.php"      class="btn btn-outline">🧾 View Orders</a>
  <a href="users.php"       class="btn btn-outline">👥 View Users</a>
</div>

<!-- RECENT ORDERS TABLE -->
<div class="card">
  <div class="card-header">
    <h2>🧾 Recent Orders</h2>
    <a href="orders.php" class="btn btn-outline btn-sm">View All →</a>
  </div>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Order #</th>
          <th>Customer</th>
          <th>Total</th>
          <th>Payment</th>
          <th>Status</th>
          <th>Date</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($o = $recentOrders->fetch_assoc()):
          $st = $o['order_status'];
          $badge = 'badge-yellow';
          if ($st === 'Delivered') $badge = 'badge-green';
          elseif ($st === 'Shipped' || $st === 'Confirmed') $badge = 'badge-blue';
          elseif ($st === 'Processing') $badge = 'badge-purple';
          elseif ($st === 'Cancelled') $badge = 'badge-red';
        ?>
        <tr>
          <td><strong><?= $o['id'] ?></strong></td>
          <td><?= e($o['name']) ?></td>
          <td><strong><?= price($o['total']) ?></strong></td>
          <td><?= e($o['payment_method']) ?></td>
          <td><span class="badge <?= $badge ?>"><?= e($o['order_status']) ?></span></td>
          <td style="color:var(--text-muted);font-size:13px"><?= date('d M Y', strtotime($o['created_at'])) ?></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require 'layout-footer.php'; ?>
