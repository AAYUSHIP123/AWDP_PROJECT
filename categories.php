<?php
$pageTitle  = 'Categories';
$activePage = 'categories';
require 'auth.php';
require '../config/database.php';
require_once '../includes/functions.php';

$message = '';
if (isset($_SESSION['category_notice'])) {
    $message = $_SESSION['category_notice'];
    unset($_SESSION['category_notice']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) && is_string($_POST['action']) ? $_POST['action'] : '';
    $name = isset($_POST['name']) && is_string($_POST['name']) ? trim($_POST['name']) : '';
    $desc = isset($_POST['description']) && is_string($_POST['description']) ? trim($_POST['description']) : '';
    $categoryId = isset($_POST['category_id']) ? filter_var($_POST['category_id'], FILTER_VALIDATE_INT) : false;
    $imageName = '';
    $uploadError = '';

    if ($action === 'add') {
        if ($name === '') {
            $message = 'Please enter a category name.';
        } else {
            $imageName = category_upload_image('image', $uploadError);
            if ($uploadError !== '') {
                $message = $uploadError;
            } else {
                $st = $conn->prepare('INSERT INTO categories(name, description, image) VALUES(?, ?, ?)');
                if (!$st) {
                    error_log('Category insert preparation failed: ' . $conn->error);
                    $message = 'The category could not be added. Please try again.';
                } else {
                    $st->bind_param('sss', $name, $desc, $imageName);
                    if ($st->execute()) {
                        $st->close();
                        header('Location: categories.php');
                        exit;
                    }
                    error_log('Category insert failed: ' . $st->error);
                    $message = $st->errno === 1062 ? 'A category with this name already exists.' : 'The category could not be added. Please try again.';
                    $st->close();
                    if ($imageName !== '') {
                        @unlink(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'categories' . DIRECTORY_SEPARATOR . $imageName);
                    }
                }
            }
        }
    } else {
        $message = 'The category action is invalid.';
    }
}

$result = $conn->query('SELECT * FROM categories ORDER BY id DESC');
if (!$result) {
    error_log('Category list query failed: ' . $conn->error);
}
require 'layout-header.php';
?>

<div style="display:grid;grid-template-columns:380px 1fr;gap:24px;align-items:start">

  <?php if ($message !== ''): ?>
    <div class="alert <?= strpos($message, 'successfully') !== false ? 'alert-success' : 'alert-danger' ?>" role="status" style="grid-column:1 / -1"><?= e($message) ?></div>
  <?php endif; ?>

  <!-- ADD FORM -->
  <div class="card">
    <div class="card-header"><h2>➕ Add Category</h2></div>
    <div class="card-body">
      <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="action" value="add">
        <div class="form-group" style="margin-bottom:16px">
          <label>Category Name *</label>
          <input type="text" name="name" maxlength="100" placeholder="e.g. Fresh Flowers" required>
        </div>
        <div class="form-group" style="margin-bottom:16px">
          <label>Description</label>
          <textarea name="description" placeholder="Short description..."></textarea>
        </div>
        <div class="form-group" style="margin-bottom:16px">
          <label>Category Image</label>
          <input type="file" name="image" accept=".jpg,.jpeg,.png,.gif,.webp,image/jpeg,image/png,image/gif,image/webp">
          <small>JPG, PNG, GIF, or WebP. Maximum 5 MB.</small>
        </div>
        <button type="submit" class="btn btn-primary">Add Category</button>
      </form>
    </div>
  </div>

  <!-- TABLE -->
  <div class="card">
    <div class="card-header"><h2>🗂️ All Categories</h2></div>
    <div class="table-wrap">
      <table>
        <thead>
          <tr><th>#</th><th>Name</th><th>Description</th><th>Image</th><th>Status</th><th>Action</th></tr>
        </thead>
        <tbody>
          <?php if ($result): ?>
            <?php while ($c = $result->fetch_assoc()): ?>
            <?php $categoryImage = category_image_url($c['image']); ?>
            <tr>
              <td><?= (int)$c['id'] ?></td>
              <td><strong><?= e($c['name']) ?></strong></td>
              <td style="color:var(--text-muted);font-size:13px"><?= e($c['description']) ?></td>
              <td class="category-image-cell">
                <?php if ($categoryImage !== ''): ?>
                  <img src="../<?= e($categoryImage) ?>" alt="<?= e($c['name']) ?>">
                <?php else: ?>
                  <span>No image</span>
                <?php endif; ?>
              </td>
              <td><span class="badge <?= $c['status'] === 'active' ? 'badge-green' : 'badge-red' ?>"><?= e($c['status']) ?></span></td>
              <td>
                <a href="edit-category.php?id=<?= (int)$c['id'] ?>" class="btn btn-warning btn-sm">✏️ Edit</a>
                <a href="delete-category.php?id=<?= (int)$c['id'] ?>" class="btn btn-danger btn-sm"
                   onclick="return confirm('Delete category?')">🗑️ Delete</a>
              </td>
            </tr>
            <?php endwhile; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

<?php require 'layout-footer.php'; ?>
