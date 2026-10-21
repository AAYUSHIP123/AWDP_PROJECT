<?php
$pageTitle  = 'Orders';
$activePage = 'orders';
require '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id        = (int)$_POST['id'];
    $newStatus = $_POST['status'];

    // 1. Get current status of the order before update
    $curr = $conn->query("SELECT order_status FROM orders WHERE id=$id")->fetch_assoc();
    $oldStatus = $curr ? $curr['order_status'] : '';

    // 2. Adjust product stock based on status changes
    if ($oldStatus !== $newStatus) {
        // Order is now DELIVERED -> Decrement stock
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
        // Order was previously DELIVERED and is now changed to Cancelled (or another status) -> Restore/Increment stock
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

    // 3. Update order status & payment status
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
    header('Location: orders.php');
    exit;
}
$result = $conn->query('SELECT * FROM orders ORDER BY id DESC');
require 'layout-header.php';

$statuses = ['Pending','Confirmed','Processing','Shipped','Delivered','Cancelled'];
?>

<div class="card">
  <div class="card-header"><h2>🧾 All Orders</h2></div>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Order #</th>
          <th>Customer</th>
          <th>Date</th>
          <th>Total</th>
          <th>Payment</th>
          <th>Status</th>
          <th>Update Status</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($o = $result->fetch_assoc()):
          $st = $o['order_status'];
          $badge = 'badge-yellow';
          if ($st === 'Delivered') $badge = 'badge-green';
          elseif ($st === 'Shipped' || $st === 'Confirmed') $badge = 'badge-blue';
          elseif ($st === 'Processing') $badge = 'badge-purple';
          elseif ($st === 'Cancelled') $badge = 'badge-red';
        ?>
        <tr>
          <td><strong><?= $o['id'] ?></strong></td>
          <td>
            <strong><?= e($o['name']) ?></strong>
            <div style="font-size:12px;color:var(--text-muted)"><?= e($o['email']) ?></div>
          </td>
          <td style="font-size:13px;color:var(--text-muted)"><?= date('d M Y', strtotime($o['created_at'])) ?></td>
          <td><strong><?= price($o['total']) ?></strong></td>
          <td style="font-size:13px"><?= e($o['payment_method']) ?></td>
          <td><span class="badge <?= $badge ?>"><?= e($o['order_status']) ?></span></td>
          <td>
            <div style="display:flex;gap:6px;align-items:center;">
              <form method="post" style="display:flex;gap:6px;align-items:center;">
                <input type="hidden" name="id" value="<?= $o['id'] ?>">
                <select name="status" style="padding:6px 10px;border:1.5px solid var(--border);border-radius:7px;font-size:13px">
                  <?php foreach ($statuses as $s): ?>
                    <option <?= $o['order_status'] === $s ? 'selected' : '' ?>><?= $s ?></option>
                  <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-primary btn-sm">Save</button>
              </form>
              <a href="order-details.php?id=<?= $o['id'] ?>" class="btn btn-outline btn-sm">👁️ Details</a>
            </div>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require 'layout-footer.php'; ?>
