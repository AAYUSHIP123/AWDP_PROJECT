<?php
session_start();
require '../config/database.php';
require '../includes/functions.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $st = $conn->prepare('SELECT * FROM admins WHERE username=?');
    $st->bind_param('s', $username);
    $st->execute();
    $a = $st->get_result()->fetch_assoc();
    if ($a && password_verify($password, $a['password'])) {
        $_SESSION['admin_id']       = $a['id'];
        $_SESSION['admin_username'] = $a['username'];
        header('Location: dashboard.php');
        exit;
    }
    $message = 'Invalid username or password.';
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Login — Wildflower Studio</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="admin-login-wrap">
  <div class="login-card">
    <div class="login-logo">
      <span class="icon">🌸</span>
      <h1>Wildflower Studio</h1>
      <p>Admin Panel — Please sign in</p>
    </div>

    <?php if ($message): ?>
      <div class="alert alert-danger">⚠️ <?= e($message) ?></div>
    <?php endif; ?>

    <form method="post">
      <div class="form-group">
        <label>Username</label>
        <input type="text" name="username" placeholder="Enter username" required autofocus>
      </div>
      <div class="form-group">
        <label>Password</label>
        <div class="password-wrap">
          <input type="password" id="adminPassword" name="password" placeholder="Enter password" required>
          <button type="button" class="toggle-password" onclick="togglePass(this, 'adminPassword')" title="Show password">👁️</button>
        </div>
      </div>
      <button type="submit" class="login-btn">Sign In →</button>
    </form>

    <script>
    function togglePass(btn, id) {
      var el = document.getElementById(id);
      if (el.type === 'password') {
        el.type = 'text';
        btn.textContent = '🙈';
        btn.title = 'Hide password';
      } else {
        el.type = 'password';
        btn.textContent = '👁️';
        btn.title = 'Show password';
      }
    }
    </script>

    <div class="view-site-link"><a href="../index.php">← Back to Store</a></div>
  </div>
</div>
</body>
</html>
