<?php
$pdo = db();

$q = trim((string)($_GET['q'] ?? ''));

$sql = "SELECT id, full_name, email, phone, identity_number, role, status, is_permanent, access_until, created_at
        FROM users
        WHERE 1=1 ";
$params = [];

if ($q !== '') {
  $sql .= " AND (
    email LIKE ? OR phone LIKE ? OR identity_number LIKE ? OR full_name LIKE ?
  )";
  $like = "%$q%";
  $params = [$like,$like,$like,$like];
}

$sql .= " ORDER BY id DESC LIMIT 200";
$st = $pdo->prepare($sql);
$st->execute($params);
$rows = $st->fetchAll();
?>

<div class="card">
  <form class="row" method="get">
    <input type="hidden" name="page" value="users"/>
    <input class="input" name="q" value="<?= htmlspecialchars($q) ?>"
      placeholder="بحث: بريد / جوال / هوية / اسم" style="min-width:280px"/>
    <button>بحث</button>
    <a class="small" href="index.php?page=users">مسح</a>
  </form>
</div>

<div class="card" style="margin-top:12px">
  <table class="table">
    <thead>
      <tr>
        <th>المستخدم</th>
        <th>الدور</th>
        <th>الحالة</th>
        <th>الصلاحية</th>
        <th>تاريخ الإنشاء</th>
        <th>إجراء</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($rows as $u): ?>
        <?php
          $role = $u['role'];
          $status = $u['status'];
          $perm = (int)$u['is_permanent'] === 1;
          $until = $u['access_until'];
        ?>
        <tr>
          <td>
            <b><?= htmlspecialchars($u['full_name']) ?></b>
            <div class="small">
              <?= htmlspecialchars($u['email']) ?> — <?= htmlspecialchars($u['phone'] ?? '—') ?>
              <?= $u['identity_number'] ? ' — هوية: '.htmlspecialchars($u['identity_number']) : '' ?>
            </div>
          </td>
          <td><?= htmlspecialchars($role) ?></td>
          <td>
            <?php if($status==='active'): ?>
              <span class="badge ok">نشط</span>
            <?php else: ?>
              <span class="badge danger">موقوف</span>
            <?php endif; ?>
          </td>
          <td>
            <?php if($perm): ?>
              <span class="badge ok">دائم</span>
            <?php else: ?>
              <span class="badge warn">مؤقت حتى: <?= htmlspecialchars((string)$until) ?></span>
            <?php endif; ?>
          </td>
          <td class="small"><?= htmlspecialchars($u['created_at']) ?></td>
          <td>
            <a href="index.php?page=user_edit&id=<?= (int)$u['id'] ?>">
              <button type="button" class="light">تعديل</button>
            </a>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if(!$rows): ?>
        <tr><td colspan="6" class="small">لا توجد نتائج</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>