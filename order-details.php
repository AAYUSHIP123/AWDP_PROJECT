<?php
$pageTitle  = 'Order Details';
$activePage = 'orders';
require '../config/database.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    header('Location: orders.php');
    exit;
}

// Handle Status Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $newStatus = $_POST['status'];

    $curr = $conn->query("SELECT order_status FROM orders WHERE id=$id")->fetch_assoc();
    $oldStatus = $curr ? $curr['order_status'] : '';

    if ($oldStatus !== $newStatus) {
        // Delivered -> Decrement stock
        if ($newStatus === 'Delivered' && $oldStatus !== 'Delivered') {
            $items = $conn->query("SELECT product_id, quantity FROM order_items WHERE order_id=$id");
            if ($items) {
                $up = $conn->prepare("UPDATE products SET stock = GREATEST(0, stock - ?) WHERE id = ?");
                while ($row = $items->fetch_assoc()) {
                    $pid = (int)$row['product_id'];
                    $qty = (int)$row['quantity'];
                    $up->bind_param('ii', $qty, $pid);
                    $up->execute();
                }
            }
        }
        // Delivered to Cancelled -> Restore stock
        elseif ($oldStatus === 'Delivered' && $newStatus !== 'Delivered') {
            $items = $conn->query("SELECT product_id, quantity FROM order_items WHERE order_id=$id");
            if ($items) {
                $up = $conn->prepare("UPDATE products SET stock = stock + ? WHERE id = ?");
                while ($row = $items->fetch_assoc()) {
                    $pid = (int)$row['product_id'];
                    $qty = (int)$row['quantity'];
                    $up->bind_param('ii', $qty, $pid);
                    $up->execute();
                }
            }
        }
    }

    if ($newStatus === 'Delivered') {
        $st = $conn->prepare("UPDATE orders SET order_status=?, payment_status='Paid' WHERE id=?");
        $st->bind_param('si', $newStatus, $id);
    } elseif ($newStatus === 'Cancelled') {
        $st = $conn->prepare("UPDATE orders SET order_status=?, payment_status='Cancelled' WHERE id=?");
        $st->bind_param('si', $newStatus, $id);
    } else {
        $st = $conn->prepare('UPDATE orders SET order_status=? WHERE id=?');
        $st->bind_param('si', $newStatus, $id);
    }
    $st->execute();
    header("Location: order-details.php?id=$id&saved=1");
    exit;
}

$st = $conn->prepare('SELECT * FROM orders WHERE id=?');
$st->bind_param('i', $id);
$st->execute();
$o = $st->get_result()->fetch_assoc();
if (!$o) {
    header('Location: orders.php');
    exit;
}

$items = $conn->query("SELECT oi.*, p.image FROM order_items oi LEFT JOIN products p ON oi.product_id = p.id WHERE oi.order_id=$id");

require 'layout-header.php';
$statuses = ['Pending','Confirmed','Processing','Shipped','Delivered','Cancelled'];
?>

<div style="margin-bottom:16px;">
  <a href="orders.php" style="color:var(--primary);font-size:14px;font-weight:600;">← Back to All Orders</a>
</div>

<?php if (isset($_GET['saved'])): ?>
  <div class="alert alert-success" style="background:#e8f5e9;color:#2e7d32;padding:12px 18px;border-radius:8px;margin-bottom:18px;font-weight:600;">
    ✓ Order status updated successfully!
  </div>
<?php endif; ?>

<div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-bottom:24px;">
  <div>
    <h1 style="font-size:24px;font-weight:800;color:var(--text);">Order #<?= $o['id'] ?></h1>
    <p style="font-size:13px;color:var(--text-muted);">Placed on <?= date('d M Y, h:i A', strtotime($o['created_at'])) ?></p>
  </div>

  <div style="display:flex;gap:10px;align-items:center;">
    <button onclick="window.print()" class="btn btn-outline" style="cursor:pointer;padding:8px 16px;font-size:13px;">
      🖨️ Print Packing Slip
    </button>
  </div>
</div>

<div style="display:grid;grid-template-columns:1fr 340px;gap:24px;align-items:start;">
  
  <!-- LEFT: ORDER ITEMS -->
  <div class="card">
    <div class="card-header">
      <h2>📦 Ordered Products</h2>
    </div>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Product</th>
            <th>Unit Price</th>
            <th>Quantity</th>
            <th>Total</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($item = $items->fetch_assoc()): ?>
          <tr>
            <td style="display:flex;align-items:center;gap:12px;">
              <?php if (!empty($item['image'])): ?>
                <img src="../<?= product_image_url($item['image']) ?>" alt="<?= e($item['name']) ?>" style="width:48px;height:48px;object-fit:cover;border-radius:8px;border:1px solid var(--border);">
              <?php else: ?>
                <div style="width:48px;height:48px;background:#fce4ec;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:20px;">🌸</div>
              <?php endif; ?>
              <div>
                <strong><?= e($item['name']) ?></strong>
                <div style="font-size:12px;color:var(--text-muted);">Item ID: <?= $item['product_id'] ?></div>
              </div>
            </td>
            <td><?= price($item['price']) ?></td>
            <td><strong>× <?= $item['quantity'] ?></strong></td>
            <td><strong style="color:var(--primary);"><?= price($item['price'] * $item['quantity']) ?></strong></td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>

    <div style="padding:18px 24px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;">
      <div style="text-align:right;">
        <div style="font-size:14px;color:var(--text-muted);margin-bottom:4px;">Grand Total:</div>
        <div style="font-size:24px;font-weight:900;color:var(--primary);"><?= price($o['total']) ?></div>
      </div>
    </div>
  </div>

  <!-- RIGHT: STATUS & CUSTOMER INFO -->
  <div>
    <!-- STATUS MANAGEMENT CARD -->
    <div class="card" style="margin-bottom:20px;">
      <div class="card-header">
        <h2>⚡ Order Status</h2>
      </div>
      <div style="padding:20px;">
        <form method="post">
          <label style="font-size:13px;font-weight:600;display:block;margin-bottom:6px;">Current Status:</label>
          <select name="status" style="width:100%;padding:10px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:14px;margin-bottom:14px;">
            <?php foreach ($statuses as $s): ?>
              <option <?= $o['order_status'] === $s ? 'selected' : '' ?>><?= $s ?></option>
            <?php endforeach; ?>
          </select>

          <button type="submit" name="update_status" class="btn btn-primary" style="width:100%;padding:10px;">
            Save Status
          </button>
        </form>

        <div style="margin-top:14px;font-size:12px;color:var(--text-muted);line-height:1.5;background:#f9f9f9;padding:10px;border-radius:6px;">
          💡 <strong>Real-World Rule:</strong> Marking order as <em>Delivered</em> decrements product stock and credits Total Revenue. Marking as <em>Cancelled</em> restores stock.
        </div>
      </div>
    </div>

    <!-- CUSTOMER & DELIVERY CARD -->
    <div class="card">
      <div class="card-header">
        <h2>📍 Shipping & Contact</h2>
      </div>
      <div style="padding:20px;font-size:14px;">
        <p style="font-weight:700;margin-bottom:6px;"><?= e($o['name']) ?></p>
        <p style="color:var(--text-muted);line-height:1.6;margin-bottom:12px;">
          <?= nl2br(e($o['shipping_address'])) ?><br>
          <?= e($o['city']) ?>, <?= e($o['state']) ?> - <?= e($o['pincode']) ?>
        </p>
        
        <div style="padding-top:10px;border-top:1px solid var(--border);">
          <div style="margin-bottom:6px;">📞 <strong>Mobile:</strong> <?= e($o['mobile']) ?></div>
          <div style="margin-bottom:6px;">✉️ <strong>Email:</strong> <?= e($o['email']) ?></div>
          <div>💳 <strong>Payment:</strong> <?= e($o['payment_method']) ?> (<?= e($o['payment_status']) ?>)</div>
        </div>
      </div>
    </div>
  </div>

</div>

<?php require 'layout-footer.php'; ?>
