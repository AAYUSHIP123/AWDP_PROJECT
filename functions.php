<?php
if (session_status() === PHP_SESSION_NONE) session_start();
function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function product_image_url($image, $fallback='images/no-image.svg'){
    $image = trim((string)$image);
    if ($image === '') return $fallback;

    $safeName = basename($image);
    $uploadsPath = __DIR__ . '/../uploads/products/' . $safeName;
    $imagesPath = __DIR__ . '/../images/' . $safeName;

    if (file_exists($uploadsPath)) {
        return 'uploads/products/' . rawurlencode($safeName);
    }
    if (file_exists($imagesPath)) {
        return 'images/' . rawurlencode($safeName);
    }

    return $fallback;
}
function category_image_url($image, $fallback=''){
    $image = trim((string)$image);
    if ($image === '') return $fallback;

    $safeName = basename($image);
    $categoryPath = __DIR__ . '/../uploads/categories/' . $safeName;
    $productsPath = __DIR__ . '/../uploads/products/' . $safeName;
    $imagesPath = __DIR__ . '/../images/' . $safeName;

    if (file_exists($categoryPath)) {
        return 'uploads/categories/' . rawurlencode($safeName);
    }
    if (file_exists($productsPath)) {
        return 'uploads/products/' . rawurlencode($safeName);
    }
    if (file_exists($imagesPath)) {
        return 'images/' . rawurlencode($safeName);
    }

    return $fallback;
}
function category_upload_image($field, &$error) {
    if (!isset($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
        return '';
    }
    if ($_FILES[$field]['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($_FILES[$field]['tmp_name'])) {
        $error = 'The image upload failed. Please choose the image again.';
        return '';
    }
    if ($_FILES[$field]['size'] > 5 * 1024 * 1024) {
        $error = 'Category images must be 5 MB or smaller.';
        return '';
    }

    $imageInfo = @getimagesize($_FILES[$field]['tmp_name']);
    $extensions = [
        IMAGETYPE_JPEG => 'jpg',
        IMAGETYPE_PNG => 'png',
        IMAGETYPE_GIF => 'gif'
    ];
    if (defined('IMAGETYPE_WEBP')) {
        $extensions[IMAGETYPE_WEBP] = 'webp';
    }
    if (!$imageInfo || !isset($extensions[$imageInfo[2]])) {
        $error = 'Please upload a JPG, PNG, GIF, or WebP image.';
        return '';
    }

    $directory = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'categories';
    if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
        $error = 'The image folder could not be created. Please try again.';
        return '';
    }

    try {
        $fileName = bin2hex(random_bytes(16)) . '.' . $extensions[$imageInfo[2]];
    } catch (Exception $exception) {
        error_log('Category image filename generation failed: ' . $exception->getMessage());
        $error = 'The image could not be saved. Please try again.';
        return '';
    }

    if (!move_uploaded_file($_FILES[$field]['tmp_name'], $directory . DIRECTORY_SEPARATOR . $fileName)) {
        $error = 'The image could not be saved. Please try again.';
        return '';
    }
    return $fileName;
}
function redirect($url){ header("Location: $url"); exit; }
function user_required(){ if(empty($_SESSION['user_id'])) redirect('login.php'); }
function admin_required(){ if(empty($_SESSION['admin_id'])) redirect('login.php'); }
function cart_count(){ return array_sum($_SESSION['cart'] ?? []); }
function cart_items($conn){
    $items=[]; $cart=$_SESSION['cart'] ?? [];
    if(!$cart) return $items;
    $ids=implode(',', array_map('intval',array_keys($cart)));
    $result=$conn->query("SELECT * FROM products WHERE id IN ($ids) AND status='active'");
    while($p=$result->fetch_assoc()){ $p['qty']=max(1,(int)$cart[$p['id']]); $items[]=$p; }
    return $items;
}
function cart_total($items){ $total=0; foreach($items as $p) $total += $p['price']*$p['qty']; return $total; }
function price($n){ return '₹'.number_format((float)$n,2); }
?>
