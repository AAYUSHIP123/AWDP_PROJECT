<?php
$pageTitle  = 'Users';
$activePage = 'users';
require '../config/database.php';
require 'layout-header.php';

$result = $conn->query('SELECT id,name,email,mobile,city,created_at FROM users ORDER BY id DESC');
?>

<div class="card">
  <div class="card-header">
    <h2>👥 Registered Users</h2>
    <span style="font-size:13px;color:var(--text-muted)"><?= $result->num_rows ?> users</span>
  </div>
  <div class="table-wrap">
    <table>
      <thead>
        <tr><th>#</th><th>Name</th><th>Email</th><th>Mobile</th><th>City</th><th>Joined</th></tr>
      </thead>
      <tbody>
        <?php while ($u = $result->fetch_assoc()): ?>
        <tr>
          <td style="color:var(--text-muted)"><?= $u['id'] ?></td>
          <td>
            <div style="display:flex;align-items:center;gap:10px">
              <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#4f46e5,#7c3aed);display:flex;align-items:center;justify-content:center;color:#fff;font-size:13px;font-weight:700;flex-shrink:0">
                <?= strtoupper(substr($u['name'], 0, 1)) ?>
              </div>
              <strong><?= e($u['name']) ?></strong>
            </div>
          </td>
          <td style="font-size:13px"><?= e($u['email']) ?></td>
          <td style="font-size:13px"><?= e($u['mobile'] ?: '—') ?></td>
          <td style="font-size:13px"><?= e($u['city'] ?: '—') ?></td>
          <td style="font-size:13px;color:var(--text-muted)"><?= date('d M Y', strtotime($u['created_at'])) ?></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require 'layout-footer.php'; ?>
