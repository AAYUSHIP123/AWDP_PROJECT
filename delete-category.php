<?php
require 'auth.php';
require '../config/database.php';

$id = isset($_GET['id']) ? filter_var($_GET['id'], FILTER_VALIDATE_INT) : false;
if (!$id || $id < 1) {
    $_SESSION['category_notice'] = 'The selected category is invalid.';
    header('Location: categories.php');
    exit;
}

$find = $conn->prepare('SELECT name FROM categories WHERE id = ?');
if (!$find) {
    error_log('Category lookup preparation failed: ' . $conn->error);
    $_SESSION['category_notice'] = 'The category could not be deleted. Please try again.';
    header('Location: categories.php');
    exit;
}

$find->bind_param('i', $id);
if (!$find->execute()) {
    error_log('Category lookup failed: ' . $find->error);
    $find->close();
    $_SESSION['category_notice'] = 'The category could not be deleted. Please try again.';
    header('Location: categories.php');
    exit;
}

$find->bind_result($categoryName);
if (!$find->fetch()) {
    $find->close();
    $_SESSION['category_notice'] = 'That category was not found.';
    header('Location: categories.php');
    exit;
}
$find->close();

if (!$conn->begin_transaction()) {
    error_log('Category delete transaction could not start: ' . $conn->error);
    $_SESSION['category_notice'] = 'The category could not be deleted. Please try again.';
    header('Location: categories.php');
    exit;
}

$unlinkProducts = $conn->prepare(
    'UPDATE products SET category_id = NULL, category = NULL
     WHERE category_id = ? OR LOWER(TRIM(category)) = LOWER(TRIM(?))'
);
if (!$unlinkProducts) {
    error_log('Category product unlink preparation failed: ' . $conn->error);
    $conn->rollback();
    $_SESSION['category_notice'] = 'The category could not be deleted. Please try again.';
    header('Location: categories.php');
    exit;
}

$unlinkProducts->bind_param('is', $id, $categoryName);
if (!$unlinkProducts->execute()) {
    error_log('Category product unlink failed: ' . $unlinkProducts->error);
    $unlinkProducts->close();
    $conn->rollback();
    $_SESSION['category_notice'] = 'The category could not be deleted. Please try again.';
    header('Location: categories.php');
    exit;
}
$unlinkProducts->close();

$delete = $conn->prepare('DELETE FROM categories WHERE id = ?');
if (!$delete) {
    error_log('Category delete preparation failed: ' . $conn->error);
    $conn->rollback();
    $_SESSION['category_notice'] = 'The category could not be deleted. Please try again.';
    header('Location: categories.php');
    exit;
}

$delete->bind_param('i', $id);
if (!$delete->execute() || $delete->affected_rows !== 1) {
    error_log('Category delete failed: ' . $delete->error);
    $delete->close();
    $conn->rollback();
    $_SESSION['category_notice'] = 'The category could not be deleted. Please try again.';
    header('Location: categories.php');
    exit;
}
$delete->close();

if (!$conn->commit()) {
    error_log('Category deletion commit failed: ' . $conn->error);
    $conn->rollback();
    $_SESSION['category_notice'] = 'The category could not be deleted. Please try again.';
} else {
    $_SESSION['category_notice'] = 'Category deleted successfully.';
}

header('Location: categories.php');
exit;
?>
