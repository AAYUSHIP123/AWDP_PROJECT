<?php
require 'config/database.php';
require 'includes/functions.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $mobile   = trim($_POST['mobile'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if (empty($_POST['accept_terms']))
        $message = 'Please agree to the Terms & Conditions and Privacy Policy.';
    elseif ($password !== $confirm)
        $message = 'Passwords do not match.';
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL))
        $message = 'Enter a valid email address.';
    else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $st   = $conn->prepare('INSERT INTO users(name,email,mobile,password) VALUES(?,?,?,?)');
        $st->bind_param('ssss', $name, $email, $mobile, $hash);
        if ($st->execute()) {
            $_SESSION['user_id']   = $st->insert_id;
            $_SESSION['user_name'] = $name;
            redirect('index.php');
        } else {
            $message = 'Email is already registered. Try logging in.';
        }
    }
}
$title = 'Create Account';
require 'includes/header.php';
?>

<main class="auth-wrap auth-split-wrap">
  <div class="auth-split auth-register">
    <figure class="auth-photo-panel">
      <img src="images/Herosection.png" alt="A hand-tied Wildflower Studio bouquet">
      <figcaption><i class="bi bi-flower1"></i> Made with care, chosen by you</figcaption>
    </figure>
    <section class="auth-form-panel">
      <div class="auth-panel-content">
        <span class="auth-eyebrow">JOIN OUR LITTLE WORLD</span>
        <h1>Create Your Account</h1>
        <p class="auth-intro">Create an account to make your flower shopping experience more personal.</p>

        <?php if ($message): ?>
          <div class="alert alert-danger">⚠️ <?= e($message) ?></div>
        <?php endif; ?>

        <form method="post" class="auth-form">
          <div class="auth-fields-grid">
            <div class="form-group">
              <label for="regName">Full Name</label>
              <input id="regName" type="text" name="name" placeholder="Your name" required>
            </div>
            <div class="form-group">
              <label for="regMobile">Mobile Number</label>
              <input id="regMobile" type="text" name="mobile" placeholder="10-digit number" required>
            </div>
            <div class="form-group auth-field-wide">
              <label for="regEmail">Email Address</label>
              <input id="regEmail" type="email" name="email" placeholder="you@example.com" required>
            </div>
            <div class="form-group">
              <label for="regPassword">Password</label>
              <div class="password-wrap">
                <input id="regPassword" type="password" name="password" placeholder="Min 6 characters" minlength="6" required>
                <button type="button" class="toggle-password" onclick="togglePass(this, 'regPassword')" title="Show password">👁️</button>
              </div>
            </div>
            <div class="form-group">
              <label for="regConfirmPassword">Confirm Password</label>
              <div class="password-wrap">
                <input id="regConfirmPassword" type="password" name="confirm_password" placeholder="Repeat password" required>
                <button type="button" class="toggle-password" onclick="togglePass(this, 'regConfirmPassword')" title="Show password">👁️</button>
              </div>
            </div>
          </div>
          <label class="auth-terms">
            <input type="checkbox" name="accept_terms" value="1" required>
            <span>I agree to the Terms &amp; Conditions and Privacy Policy.</span>
          </label>
          <button type="submit" class="auth-btn">Create Account</button>
        </form>

        <div class="auth-footer">Already have an account? <a href="login.php">Login</a></div>
        <p class="auth-closing-line">A little beauty, delivered with love.</p>
      </div>
    </section>
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
