<?php
$ctx = must_be_site_admin_or_staff();
$pdo = db();

$q = trim((string)($_GET['q'] ?? ''));

$sql = "
  SELECT m.id, m.title, m.body, m.target_type, m.created_at,
         (SELECT COUNT(*) FROM admin_message_recipients r WHERE r.message_id=m.id) AS recipients_count
  FROM admin_messages m
  WHERE 1=1
";
$params = [];

if ($q !== '') {
  $sql .= " AND (m.title LIKE ? OR m.body LIKE ?)";
  $like = "%$q%";
  $params = [$like, $like];
}

$sql .= " ORDER BY m.id DESC LIMIT 200";

$st = $pdo->prepare($sql);
$st->execute($params);
$rows = $st->fetchAll();

function target_label(string $t): string {
  return match($t){
    'all' => 'كل المستخدمين',
    'owners_staff' => 'الملاك + التابعين',
    'tenants' => 'المستأجرين',
    'site_admin_staff' => 'مسؤولي النظام',
    'custom' => 'أشخاص محددين',
    default => $t
  };
}
?>

<div class="card">
  <form class="row" method="get">
    <input type="hidden" name="page" value="messages"/>
    <input class="input" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="بحث في العنوان/المحتوى" style="min-width:320px"/>
    <button>بحث</button>
    <a class="small" href="index.php?page=messages">مسح</a>
    <a href="index.php?page=message_send"><button type="button" class="light">إرسال رسالة</button></a>
  </form>
</div>

<div class="card" style="margin-top:12px">
  <table class="table">
    <thead>
      <tr>
        <th>الرسالة</th>
        <th>الاستهداف</th>
        <th>المستلمون</th>
        <th>التاريخ</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach($rows as $m): ?>
      <tr>
        <td>
          <b><?= htmlspecialchars((string)$m['title']) ?></b>
          <div class="small"><?= nl2br(htmlspecialchars(mb_strimwidth((string)$m['body'], 0, 180, '…', 'UTF-8'))) ?></div>
        </td>
        <td><?= htmlspecialchars(target_label((string)$m['target_type'])) ?></td>
        <td><span class="badge"><?= (int)$m['recipients_count'] ?></span></td>
        <td class="small"><?= htmlspecialchars((string)$m['created_at']) ?></td>
      </tr>
    <?php endforeach; ?>
    <?php if(!$rows): ?>
      <tr><td colspan="4" class="small">لا توجد رسائل</td></tr>
    <?php endif; ?>
    </tbody>
  </table>
</div>