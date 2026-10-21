<?php
$pageTitle  = 'Contact Messages';
$activePage = 'contacts';
require 'auth.php';
require '../config/database.php';

if (empty($_SESSION['contact_messages_csrf'])) {
    $_SESSION['contact_messages_csrf'] = bin2hex(random_bytes(32));
}

$actionMessage = $_SESSION['contact_messages_notice'] ?? '';
unset($_SESSION['contact_messages_notice']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_contact_id'])) {
    $token = isset($_POST['csrf_token']) && is_string($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
    $contactId = filter_var($_POST['delete_contact_id'], FILTER_VALIDATE_INT);

    if (!hash_equals($_SESSION['contact_messages_csrf'], $token)) {
        $_SESSION['contact_messages_notice'] = 'This action could not be verified. Please try again.';
    } elseif (!$contactId || $contactId < 1) {
        $_SESSION['contact_messages_notice'] = 'The selected message is invalid.';
    } else {
        $delete = $conn->prepare('DELETE FROM contacts WHERE id = ?');
        if (!$delete) {
            error_log('Contact message delete preparation failed: ' . $conn->error);
            $_SESSION['contact_messages_notice'] = 'The message could not be deleted. Please try again.';
        } else {
            $delete->bind_param('i', $contactId);
            if ($delete->execute()) {
                $_SESSION['contact_messages_notice'] = $delete->affected_rows > 0
                    ? 'Contact message deleted successfully.'
                    : 'That contact message was not found.';
            } else {
                error_log('Contact message delete failed: ' . $delete->error);
                $_SESSION['contact_messages_notice'] = 'The message could not be deleted. Please try again.';
            }
            $delete->close();
        }
    }

    header('Location: contact-messages.php');
    exit;
}

$result = $conn->query('SELECT id, name, phone, email, message, created_at FROM contacts ORDER BY id DESC');
require 'layout-header.php';
?>

<div class="card">
  <div class="card-header">
    <h2>✉️ Messages from Your Customers</h2>
    <span style="font-size:13px;color:var(--text-muted)"><?= $result ? (int)$result->num_rows : 0 ?> messages</span>
  </div>

  <?php if ($actionMessage !== ''): ?>
    <div class="alert <?= strpos($actionMessage, 'successfully') !== false ? 'alert-success' : 'alert-danger' ?>" role="status"><?= e($actionMessage) ?></div>
  <?php endif; ?>

  <?php if (!$result): ?>
    <div class="contact-messages-empty">
      <strong>Contact messages could not be loaded.</strong>
      <p>Please check that the contacts table is available, then reload this page.</p>
    </div>
  <?php elseif ($result->num_rows === 0): ?>
    <div class="contact-messages-empty">
      <span class="contact-empty-icon">✿</span>
      <strong>No messages yet</strong>
      <p>Messages sent through the store's contact form will appear here.</p>
    </div>
  <?php else: ?>
    <div class="table-wrap contact-messages-table-wrap">
      <table class="contact-messages-table">
        <thead>
          <tr>
            <th>Name</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Message</th>
            <th>Received</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($contact = $result->fetch_assoc()):
            $rawMessage = (string)$contact['message'];
            $lines = explode("\n", $rawMessage);
            $phone = trim((string)$contact['phone']);
            $body = $rawMessage;
            $lineIndex = 0;

            if (isset($lines[0]) && strpos(rtrim($lines[0], "\r"), 'Subject: ') === 0) {
                $lineIndex = 1;
                if ($phone === '' && isset($lines[$lineIndex]) && strpos(rtrim($lines[$lineIndex], "\r"), 'Phone: ') === 0) {
                    $phone = substr(rtrim($lines[$lineIndex], "\r"), strlen('Phone: '));
                }
                if (isset($lines[$lineIndex]) && strpos(rtrim($lines[$lineIndex], "\r"), 'Phone: ') === 0) $lineIndex++;
                if (isset($lines[$lineIndex]) && rtrim($lines[$lineIndex], "\r") === 'Message:') {
                    $lineIndex++;
                    $body = implode("\n", array_slice($lines, $lineIndex));
                } else {
                    $body = implode("\n", array_slice($lines, $lineIndex));
                }
            }
          ?>
          <tr>
            <td class="contact-name-cell">
              <div class="contact-name-content">
                <span class="contact-customer-avatar"><?= e(strtoupper(substr(trim($contact['name']), 0, 1))) ?></span>
                <strong><?= e($contact['name']) ?></strong>
              </div>
            </td>
            <td class="contact-phone-cell">
              <?php if ($phone !== ''): ?>
                <a href="tel:<?= e($phone) ?>"><?= e($phone) ?></a>
              <?php else: ?>
                <span>—</span>
              <?php endif; ?>
            </td>
            <td class="contact-email-cell"><a href="mailto:<?= e($contact['email']) ?>"><?= e($contact['email']) ?></a></td>
            <td><div class="contact-message-body"><?= nl2br(e(trim($body))) ?></div></td>
            <td class="contact-received"><?= date('d M Y, h:i A', strtotime($contact['created_at'])) ?></td>
            <td class="contact-action-cell">
              <form method="post" action="contact-messages.php" onsubmit="return confirm('Delete this contact message? This action cannot be undone.');">
                <input type="hidden" name="csrf_token" value="<?= e($_SESSION['contact_messages_csrf']) ?>">
                <input type="hidden" name="delete_contact_id" value="<?= (int)$contact['id'] ?>">
                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
              </form>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<?php require 'layout-footer.php'; ?>
