<?php
require 'config/database.php';
require 'includes/functions.php';

$message = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_SESSION['contact_success'])) {
    $message = $_SESSION['contact_success'];
    unset($_SESSION['contact_success']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = isset($_POST['name']) && is_string($_POST['name']) ? trim($_POST['name']) : '';
    $email   = isset($_POST['email']) && is_string($_POST['email']) ? trim($_POST['email']) : '';
    $phone   = isset($_POST['phone']) && is_string($_POST['phone']) ? trim($_POST['phone']) : '';
    $msgText = isset($_POST['message']) && is_string($_POST['message']) ? trim($_POST['message']) : '';

    if ($name !== '' && $phone !== '' && $email !== '' && $msgText !== '') {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';
        } else {
            $recentMessages = $conn->prepare('SELECT email, phone FROM contacts WHERE created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)');
            if (!$recentMessages) {
                error_log('Contact rate-limit query preparation failed: ' . $conn->error);
                $error = 'Something went wrong. Please try again.';
            } elseif (!$recentMessages->execute()) {
                error_log('Contact rate-limit query failed: ' . $recentMessages->error);
                $recentMessages->close();
                $error = 'Something went wrong. Please try again.';
            } else {
                $recentMessages->bind_result($recentEmail, $recentPhone);
                $normalizedPhone = preg_replace('/\D+/', '', $phone);
                $rateLimited = false;
                while ($recentMessages->fetch()) {
                    $sameEmail = strtolower($recentEmail) === strtolower($email);
                    $samePhone = $normalizedPhone !== '' &&
                        preg_replace('/\D+/', '', (string)$recentPhone) === $normalizedPhone;
                    if ($sameEmail || $samePhone) {
                        $rateLimited = true;
                        break;
                    }
                }
                $recentMessages->close();

                if ($rateLimited) {
                    $error = 'You have already sent a message with this email address or phone number within the last 24 hours. Please try again later.';
                } else {
                    $st = $conn->prepare('INSERT INTO contacts(name, phone, email, message) VALUES(?, ?, ?, ?)');
                    if (!$st) {
                        error_log('Contact message insert preparation failed: ' . $conn->error);
                        $error = 'Something went wrong. Please try again.';
                    } else {
                        $st->bind_param('ssss', $name, $phone, $email, $msgText);
                        if ($st->execute()) {
                            $_SESSION['contact_success'] = 'Thank you! Your message has been sent successfully. We will get back to you within 24 hours.';
                            header('Location: contact.php');
                            exit;
                        } else {
                            error_log('Contact message insert failed: ' . $st->error);
                            $error = 'Something went wrong. Please try again.';
                        }
                        $st->close();
                    }
                }
            }
        }
    } else {
        $error = 'Please fill out all required fields marked with (*).';
    }
}

$title = 'Contact Us - The Wildflower Studio';
require 'includes/header.php';
?>

<main class="contact-page">
  <section class="contact-intro">
    <span class="contact-eyebrow">WE'D LOVE TO HEAR FROM YOU</span>
    <h1>Let's Make Something<br>Beautiful Happen.</h1>
    <p>Have a question about a bouquet, need help with an order, or looking for the perfect floral gift? We'd love to hear from you. Send us a message and we'll be happy to help.</p>
  </section>

  <section class="contact-details" aria-label="Contact information">
    <article class="contact-detail-card">
      <span class="contact-detail-icon"><i class="bi bi-geo-alt" aria-hidden="true"></i></span>
      <div>
        <h2>Studio Location</h2>
        <p>Ahmedabad, Gujarat, India</p>
      </div>
    </article>
    <article class="contact-detail-card">
      <span class="contact-detail-icon"><i class="bi bi-whatsapp" aria-hidden="true"></i></span>
      <div>
        <h2>Phone &amp; WhatsApp</h2>
        <a href="tel:+918511264040">+91 85112 64040</a>
      </div>
    </article>
    <article class="contact-detail-card">
      <span class="contact-detail-icon"><i class="bi bi-envelope" aria-hidden="true"></i></span>
      <div>
        <h2>Email Us</h2>
        <a href="mailto:aayushikpatel7321@gmail.com">aayushikpatel7321@gmail.com</a>
      </div>
    </article>
    <article class="contact-detail-card">
      <span class="contact-detail-icon"><i class="bi bi-clock" aria-hidden="true"></i></span>
      <div>
        <h2>Response Time</h2>
        <p>Within 24 business hours</p>
      </div>
    </article>
  </section>

  <section class="contact-main">
    <aside class="contact-faq">
      <span class="contact-section-label">HERE TO HELP</span>
      <h2>Frequently Asked Questions</h2>

      <article class="contact-faq-item">
        <span class="contact-faq-icon"><i class="bi bi-flower1" aria-hidden="true"></i></span>
        <div>
          <h3>Can I order flowers for a special occasion?</h3>
          <p>Absolutely! Explore our collections to find flowers for birthdays, anniversaries, celebrations, and thoughtful surprises.</p>
        </div>
      </article>

      <article class="contact-faq-item">
        <span class="contact-faq-icon"><i class="bi bi-box-seam" aria-hidden="true"></i></span>
        <div>
          <h3>Can I check my order status?</h3>
          <p>If you've already placed an order, contact us with your order number and we'll help you with an update.</p>
        </div>
      </article>

      <article class="contact-faq-item">
        <span class="contact-faq-icon"><i class="bi bi-chat-heart" aria-hidden="true"></i></span>
        <div>
          <h3>How can I get help with an order?</h3>
          <p>Send us a message using the contact form and include your order number, if applicable.</p>
        </div>
      </article>
    </aside>

    <section class="contact-form-card">
      <span class="contact-section-label">A NOTE TO OUR STUDIO</span>
      <h2>Send Us a Message</h2>
      <p class="contact-form-intro">Have something on your mind? Fill out the form below and we'll get back to you as soon as possible.</p>

      <?php if ($message): ?>
        <div class="alert alert-success" id="contact-success-message" role="status"><?= e($message) ?></div>
      <?php elseif ($error): ?>
        <div class="alert alert-danger" role="alert">⚠️ <?= e($error) ?></div>
      <?php endif; ?>

      <form method="post" class="contact-form">
        <div class="form-group">
          <label for="contact-name">Your Full Name *</label>
          <input id="contact-name" name="name" maxlength="100" value="<?= e($_POST['name'] ?? '') ?>" placeholder="Your name" autocomplete="name" required>
        </div>

        <div class="contact-form-row">
          <div class="form-group">
            <label for="contact-phone">Phone / WhatsApp Number *</label>
            <input id="contact-phone" name="phone" type="tel" maxlength="30" value="<?= e($_POST['phone'] ?? '') ?>" placeholder="+91" autocomplete="tel" required>
          </div>
          <div class="form-group">
            <label for="contact-email">Email Address *</label>
            <input id="contact-email" type="email" name="email" maxlength="150" value="<?= e($_POST['email'] ?? '') ?>" placeholder="you@example.com" autocomplete="email" required>
          </div>
        </div>

        <div class="form-group">
          <label for="contact-message">Your Message / Note *</label>
          <textarea id="contact-message" name="message" rows="6" placeholder="Tell us how we can help..." required><?= e($_POST['message'] ?? '') ?></textarea>
        </div>

        <button type="submit" class="contact-submit-button">Send your message <i class="bi bi-flower3" aria-hidden="true"></i></button>
      </form>
    </section>
  </section>

  <section class="contact-thanks">
    <span class="contact-thanks-icon"><i class="bi bi-heart" aria-hidden="true"></i></span>
    <p>Every message matters to us. Thank you for choosing Wildflower Studio to be part of your special moments.</p>
  </section>
</main>

<script>
  window.setTimeout(function () {
    var successMessage = document.getElementById('contact-success-message');
    if (successMessage) successMessage.remove();
  }, 7000);
</script>
<?php require 'includes/footer.php'; ?>
