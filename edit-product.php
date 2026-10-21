<?php
$pageTitle  = 'Edit Product';
$activePage = 'products';
require '../config/database.php';

$id = (int)($_GET['id'] ?? 0);
$st = $conn->prepare('SELECT * FROM products WHERE id=?');
$st->bind_param('i', $id);
$st->execute();
$p = $st->get_result()->fetch_assoc();
if (!$p) die('Product not found.');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']);
    $desc     = trim($_POST['description']);
    $price    = (float)$_POST['price'];
    $discount = (float)$_POST['discount'];
    $stock    = (int)$_POST['stock'];
    $cat      = (int)$_POST['category_id'];
    $st = $conn->prepare('UPDATE products SET name=?,description=?,price=?,discount=?,stock=?,category_id=? WHERE id=?');
    $st->bind_param('ssddiiii', $name, $desc, $price, $discount, $stock, $cat, $id);
    $st->execute();
    header('Location: products.php');
    exit;
}
$cats = $conn->query('SELECT * FROM categories WHERE status="active"');
require 'layout-header.php';
?>

<div style="max-width:720px">
  <div class="card">
    <div class="card-header">
      <h2>✏️ Edit Product</h2>
      <a href="products.php" class="btn btn-outline btn-sm">← Back</a>
    </div>
    <div class="card-body">
      <form method="post">
        <div class="form-grid">
          <div class="form-group">
            <label>Product Name *</label>
            <input type="text" name="name" value="<?= e($p['name']) ?>" required>
          </div>
          <div class="form-group">
            <label>Category *</label>
            <select name="category_id" required>
              <option value="">Select category</option>
              <?php while ($c = $cats->fetch_assoc()): ?>
                <option value="<?= $c['id'] ?>" <?= $c['id'] == $p['category_id'] ? 'selected' : '' ?>>
                  <?= e($c['name']) ?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Price (₹) *</label>
            <input type="number" step="0.01" name="price" value="<?= $p['price'] ?>" required>
          </div>
          <div class="form-group">
            <label>Discount (₹)</label>
            <input type="number" step="0.01" name="discount" value="<?= $p['discount'] ?>">
          </div>
          <div class="form-group">
            <label>Stock *</label>
            <input type="number" name="stock" value="<?= $p['stock'] ?>" min="0" required>
          </div>
          <div class="form-group">
            <label>Current Image</label>
            <input type="text" value="<?= e($p['image'] ?: 'None') ?>" disabled style="background:#f8fafc;color:var(--text-muted)">
          </div>
          <div class="form-group full">
            <label>Description *</label>
            <textarea name="description" required><?= e($p['description']) ?></textarea>
          </div>
        </div>
        <div style="margin-top:8px;display:flex;gap:12px">
          <button type="submit" class="btn btn-primary">💾 Update Product</button>
          <a href="products.php" class="btn btn-outline">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require 'layout-footer.php'; ?>
