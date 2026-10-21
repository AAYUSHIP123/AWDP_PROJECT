<?php
require 'config/database.php';
require 'includes/functions.php';

$message = '';
$error   = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['new_password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid registered email address.';
    } elseif (strlen($password) < 6) {
        $error = 'New password must be at least 6 characters long.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match. Please re-enter carefully.';
    } else {
        // Check if user exists with this email
        $st = $conn->prepare('SELECT id, name FROM users WHERE email=?');
        $st->bind_param('s', $email);
        $st->execute();
        $user = $st->get_result()->fetch_assoc();

        if (!$user) {
            $error = 'No account found with this email address. Please check or register.';
        } else {
            // Hash and update new password
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $up   = $conn->prepare('UPDATE users SET password=? WHERE id=?');
            $up->bind_param('si', $hash, $user['id']);
            if ($up->execute()) {
                $success = true;
                $message = 'Your password has been reset successfully! You can now sign in with your new password.';
            } else {
                $error = 'Could not update password. Please try again.';
            }
        }
    }
}

$title = 'Reset Password - The Wildflower Studio';
require 'includes/header.php';
?>

<main class="auth-wrap auth-split-wrap">
  <div class="auth-split auth-login">
    <section class="auth-form-panel">
      <div class="auth-panel-content">
        <span class="auth-eyebrow">A FRESH START</span>
        <h1>Reset Your Password.</h1>
        <p class="auth-intro">Choose a new password to get back to your favourite blooms.</p>

        <?php if ($success): ?>
          <div class="alert alert-success auth-reset-success">
            <strong>✓ Password Reset Successful!</strong>
            <span><?= e($message) ?></span>
          </div>
          <a href="login.php" class="auth-btn auth-reset-continue">Proceed to Login</a>
        <?php else: ?>
          <?php if ($error): ?>
            <div class="alert alert-danger">⚠️ <?= e($error) ?></div>
          <?php endif; ?>

          <form method="post" class="auth-form auth-login-form">
            <div class="form-group">
              <label for="resetEmail">Registered Email Address</label>
              <input id="resetEmail" type="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" placeholder="you@example.com" required autofocus>
            </div>

            <div class="form-group">
              <label for="resetPassword">New Password</label>
              <div class="password-wrap">
                <input id="resetPassword" type="password" name="new_password" placeholder="Min 6 characters" minlength="6" required>
                <button type="button" class="toggle-password" onclick="togglePass(this, 'resetPassword')" title="Show password">👁️</button>
              </div>
            </div>

            <div class="form-group">
              <label for="resetConfirmPassword">Confirm New Password</label>
              <div class="password-wrap">
                <input id="resetConfirmPassword" type="password" name="confirm_password" placeholder="Re-enter new password" minlength="6" required>
                <button type="button" class="toggle-password" onclick="togglePass(this, 'resetConfirmPassword')" title="Show password">👁️</button>
              </div>
            </div>

            <button type="submit" class="auth-btn">Update &amp; Reset Password</button>
          </form>
        <?php endif; ?>

        <div class="auth-footer">Remembered your password? <a href="login.php">Back to Login</a></div>
        <p class="auth-closing-line">Your next favourite bouquet is waiting.</p>
      </div>
    </section>
    <figure class="auth-photo-panel">
      <img src="images/Herosection.png" alt="A hand-tied Wildflower Studio bouquet">
      <figcaption><i class="bi bi-flower1"></i> Find the flowers that feel like you</figcaption>
    </figure>
  </div>
</main>

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

<?php require 'includes/footer.php'; ?>