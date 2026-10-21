<?php
$pageTitle = 'Edit Category';
$activePage = 'categories';
require 'auth.php';
require '../config/database.php';
require_once '../includes/functions.php';

$categoryId = isset($_GET['id']) ? filter_var($_GET['id'], FILTER_VALIDATE_INT) : false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoryId = isset($_POST['id']) ? filter_var($_POST['id'], FILTER_VALIDATE_INT) : false;
}
if (!$categoryId || $categoryId < 1) {
    $_SESSION['category_notice'] = 'The selected category is invalid.';
    header('Location: categories.php');
    exit;
}

$select = $conn->prepare('SELECT name, description, image, status FROM categories WHERE id = ?');
if (!$select) {
    error_log('Category edit lookup preparation failed: ' . $conn->error);
    $_SESSION['category_notice'] = 'The category could not be loaded. Please try again.';
    header('Location: categories.php');
    exit;
}
$select->bind_param('i', $categoryId);
if (!$select->execute()) {
    error_log('Category edit lookup failed: ' . $select->error);
    $select->close();
    $_SESSION['category_notice'] = 'The category could not be loaded. Please try again.';
    header('Location: categories.php');
    exit;
}
$select->bind_result($name, $description, $image, $status);
if (!$select->fetch()) {
    $select->close();
    $_SESSION['category_notice'] = 'That category was not found.';
    header('Location: categories.php');
    exit;
}
$select->close();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) && is_string($_POST['name']) ? trim($_POST['name']) : '';
    $description = isset($_POST['description']) && is_string($_POST['description']) ? trim($_POST['description']) : '';
    $status = isset($_POST['status']) && in_array($_POST['status'], ['active', 'inactive'], true) ? $_POST['status'] : '';

    if ($name === '' || strlen($name) > 100) {
        $error = 'Enter a category name up to 100 characters.';
    } elseif ($status === '') {
        $error = 'Choose a valid category status.';
    } else {
        $uploadError = '';
        $newImage = category_upload_image('image', $uploadError);
        if ($uploadError !== '') {
            $error = $uploadError;
        } else {
            if ($newImage !== '') {
                $update = $conn->prepare('UPDATE categories SET name = ?, description = ?, image = ?, status = ? WHERE id = ?');
            } else {
                $update = $conn->prepare('UPDATE categories SET name = ?, description = ?, status = ? WHERE id = ?');
            }

            if (!$update) {
                error_log('Category update preparation failed: ' . $conn->error);
                $error = 'The category could not be updated. Please try again.';
            } else {
                if ($newImage !== '') {
                    $update->bind_param('ssssi', $name, $description, $newImage, $status, $categoryId);
                } else {
                    $update->bind_param('sssi', $name, $description, $status, $categoryId);
                }

                if ($update->execute()) {
                    $update->close();
                    if ($newImage !== '' && $image !== '' && basename($image) === $image) {
                        $oldPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'categories' . DIRECTORY_SEPARATOR . $image;
                        if (is_file($oldPath) && !unlink($oldPath)) {
                            error_log('Could not remove replaced category image: ' . $oldPath);
                        }
                    }
                    header('Location: categories.php');
                    exit;
                }

                error_log('Category update failed: ' . $update->error);
                $error = $update->errno === 1062
                    ? 'A category with this name already exists.'
                    : 'The category could not be updated. Please try again.';
                $update->close();
                if ($newImage !== '') {
                    $newPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'categories' . DIRECTORY_SEPARATOR . $newImage;
                    if (is_file($newPath) && !unlink($newPath)) {
                        error_log('Could not remove unused category image: ' . $newPath);
                    }
                }
            }
        }
    }
}

$imageUrl = category_image_url($image);
require 'layout-header.php';
?>

<div style="max-width:720px">
  <div class="card">
    <div class="card-header">
      <h2>✏️ Edit Category</h2>
      <a href="categories.php" class="btn btn-outline btn-sm">← Back</a>
    </div>
    <div class="card-body">
      <?php if ($error !== ''): ?>
        <div class="alert alert-danger" role="alert"><?= e($error) ?></div>
      <?php endif; ?>
      <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= (int)$categoryId ?>">
        <div class="form-group" style="margin-bottom:16px">
          <label for="category-name">Category Name *</label>
          <input id="category-name" type="text" name="name" maxlength="100" value="<?= e($name) ?>" required>
        </div>
        <div class="form-group" style="margin-bottom:16px">
          <label for="category-description">Description</label>
          <textarea id="category-description" name="description"><?= e($description) ?></textarea>
        </div>
        <div class="form-group" style="margin-bottom:16px">
          <label for="category-status">Status</label>
          <select id="category-status" name="status" required>
            <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active</option>
            <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Inactive</option>
          </select>
        </div>
        <div class="form-group" style="margin-bottom:16px">
          <label for="category-image">Category Image</label>
          <?php if ($imageUrl !== ''): ?>
            <img class="category-edit-preview" src="../<?= e($imageUrl) ?>" alt="<?= e($name) ?>">
          <?php endif; ?>
          <input id="category-image" type="file" name="image" accept=".jpg,.jpeg,.png,.gif,.webp,image/jpeg,image/png,image/gif,image/webp">
          <small>Choose a new image to replace the current one. JPG, PNG, GIF, or WebP; maximum 5 MB.</small>
        </div>
        <button type="submit" class="btn btn-primary">Save Changes</button>
        <a href="categories.php" class="btn btn-outline">Cancel</a>
      </form>
    </div>
  </div>
</div>

<?php require 'layout-footer.php'; ?>
