<?php
$pageTitle  = 'Add Product';
$activePage = 'add-product';
require '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']);
    $desc     = trim($_POST['description']);
    $price    = (float)$_POST['price'];
    $discount = (float)$_POST['discount'];
    $stock    = (int)$_POST['stock'];
    $cat      = (int)$_POST['category_id'];
    $image    = '';
    if (!empty($_FILES['image']['name'])) {
        $uploadDir = '../uploads/products/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $original = basename($_FILES['image']['name']);
        $ext      = strtolower(pathinfo($original, PATHINFO_EXTENSION));
        $base     = preg_replace('/[^A-Za-z0-9._-]+/', '-', pathinfo($original, PATHINFO_FILENAME)) ?: 'product';
        $image    = time() . '_' . $base . '.' . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $image);
    }
    $st = $conn->prepare('INSERT INTO products(name,description,price,discount,stock,category_id,image,status) VALUES(?,?,?,?,?,?,?,"active")');
    $st->bind_param('ssddiis', $name, $desc, $price, $discount, $stock, $cat, $image);
    $st->execute();
    header('Location: products.php');
    exit;
}
$cats = $conn->query("SELECT * FROM categories WHERE status='active'");
require 'layout-header.php';
?>

<div style="max-width:720px">
  <div class="card">
    <div class="card-header">
      <h2>➕ Add New Product</h2>
      <a href="products.php" class="btn btn-outline btn-sm">← Back</a>
    </div>
    <div class="card-body">
      <form method="post" enctype="multipart/form-data">
        <div class="form-grid">
          <div class="form-group">
            <label>Product Name *</label>
            <input type="text" name="name" placeholder="e.g. Rose Gold Ring" required>
          </div>
          <div class="form-group">
            <label>Category *</label>
            <select name="category_id" required>
              <option value="">Select category</option>
              <?php while ($c = $cats->fetch_assoc()): ?>
                <option value="<?= $c['id'] ?>"><?= e($c['name']) ?></option>
              <?php endwhile; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Price (₹) *</label>
            <input type="number" step="0.01" name="price" placeholder="0.00" required>
          </div>
          <div class="form-group">
            <label>Discount (₹)</label>
            <input type="number" step="0.01" name="discount" value="0">
          </div>
          <div class="form-group">
            <label>Stock Quantity *</label>
            <input type="number" name="stock" min="0" value="1" required>
          </div>
          <div class="form-group">
            <label>Product Image</label>
            <input type="file" name="image" accept="image/*">
          </div>
          <div class="form-group full">
            <label>Description *</label>
            <textarea name="description" placeholder="Describe the product..." required></textarea>
          </div>
        </div>
        <div style="margin-top:8px;display:flex;gap:12px">
          <button type="submit" class="btn btn-primary">➕ Add Product</button>
          <a href="products.php" class="btn btn-outline">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require 'layout-footer.php'; ?>
