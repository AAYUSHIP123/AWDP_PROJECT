<?php
$pageTitle  = 'Products';
$activePage = 'products';
require '../config/database.php';
require 'layout-header.php';

$result = $conn->query('SELECT p.*, c.name AS cat_name FROM products p LEFT JOIN categories c ON p.category_id=c.id ORDER BY p.id DESC');
?>

<div class="card">
  <div class="card-header">
    <h2>📦 All Products</h2>
    <a href="add-product.php" class="btn btn-primary">➕ Add Product</a>
  </div>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Product</th>
          <th>Category</th>
          <th>Price</th>
          <th>Stock</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($p = $result->fetch_assoc()):
          $statusBadge = $p['status'] === 'active' ? 'badge-green' : 'badge-red';
        ?>
        <tr>
          <td style="color:var(--text-muted)"><?= $p['id'] ?></td>
          <td>
            <strong><?= e($p['name']) ?></strong>
            <?php if ($p['image']): ?>
              <div style="font-size:11px;color:var(--text-muted)">Has image</div>
            <?php endif; ?>
          </td>
          <td><?= e($p['cat_name'] ?? $p['category'] ?? '—') ?></td>
          <td><strong><?= price($p['price']) ?></strong>
            <?php if ($p['discount'] > 0): ?>
              <div style="font-size:11px;color:var(--text-muted)">Disc: <?= price($p['discount']) ?></div>
            <?php endif; ?>
          </td>
          <td>
            <span class="badge <?= $p['stock'] > 0 ? 'badge-blue' : 'badge-red' ?>">
              <?= $p['stock'] ?> left
            </span>
          </td>
          <td><span class="badge <?= $statusBadge ?>"><?= e($p['status']) ?></span></td>
          <td>
            <div style="display:flex;gap:6px;flex-wrap:wrap">
              <a href="edit-product.php?id=<?= $p['id'] ?>" class="btn btn-outline btn-sm">✏️ Edit</a>
              <a href="toggle-product.php?id=<?= $p['id'] ?>" class="btn btn-warning btn-sm">🔄 Toggle</a>
              <a href="delete-product.php?id=<?= $p['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this product?')">🗑️ Delete</a>
            </div>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require 'layout-footer.php'; ?>
