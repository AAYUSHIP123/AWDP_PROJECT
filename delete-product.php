<?php require 'auth.php';require '../config/database.php';$id=(int)($_GET['id']??0);$conn->query("DELETE FROM products WHERE id=$id");header('Location: products.php');exit; ?>
