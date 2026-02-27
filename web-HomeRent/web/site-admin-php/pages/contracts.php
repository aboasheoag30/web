<?php
require_once __DIR__ . '/../../api/_core/db.php';
require_once __DIR__ . '/../../api/_core/auth.php';

$auth = require_auth();
$wsId = (int)($auth['workspace_id'] ?? 0);
$role = (string)($auth['role'] ?? '');

if ($wsId <= 0) {
  http_response_code(401);
  exit('Unauthorized');
}

// (اختياري) لو تبيها "للمالك فقط"
if ($role !== 'owner') {
  http_response_code(403);
  exit('Forbidden');
}

$pdo = db();
$q = trim((string)($_GET['q'] ?? ''));

$sql = "
SELECT
  c.id,
  c.contract_number,
  c.start_date,
  c.end_date,
  c.status,
  t.full_name AS tenant_name,
  t.phone AS tenant_phone,
  p.name AS property_name,
  u.unit_type,
  u.unit_number
FROM contracts c
LEFT JOIN tenants t ON t.id = c.tenant_id
LEFT JOIN properties p ON p.id = c.property_id
LEFT JOIN units u ON u.id = c.unit_id
WHERE c.workspace_id = ?
";

$params = [$wsId];

if ($q !== '') {
  $sql .= " AND (
    c.contract_number LIKE ?
    OR p.name LIKE ?
    OR t.full_name LIKE ?
    OR t.phone LIKE ?
    OR u.unit_type LIKE ?
    OR u.unit_number LIKE ?
  )";
  $like = "%$q%";
  array_push($params, $like,$like,$like,$like,$like,$like);
}

$sql .= " ORDER BY c.id DESC LIMIT 200";

$st = $pdo->prepare($sql);
$st->execute($params);
$rows = $st->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card">
  <div class="row" style="align-items:center; justify-content:space-between; gap:12px;">
    <h2 style="margin:0;">العقود</h2>

    <form class="row" method="get" style="gap:8px;">
      <input type="hidden" name="page" value="contracts"/>
      <input class="input" name="q" value="<?= htmlspecialchars($q) ?>"
        placeholder="بحث: رقم العقد / العقار / المستأجر / الجوال / الوحدة" />
      <button class="btn" type="submit">بحث</button>
    </form>
  </div>

  <div style="overflow:auto; margin-top:12px;">
    <table class="table">
      <thead>
        <tr>
          <th>#</th>
          <th>رقم العقد</th>
          <th>العقار</th>
          <th>الوحدة</th>
          <th>المستأجر</th>
          <th>الجوال</th>
          <th>بداية</th>
          <th>نهاية</th>
          <th>الحالة</th>
        </tr>
      </thead>
      <tbody>
      <?php if (!$rows): ?>
        <tr><td colspan="9" style="text-align:center; padding:16px;">لا توجد عقود</td></tr>
      <?php else: ?>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?= (int)$r['id'] ?></td>
            <td><?= htmlspecialchars((string)$r['contract_number']) ?></td>
            <td><?= htmlspecialchars((string)$r['property_name']) ?></td>
            <td><?= htmlspecialchars(trim((string)$r['unit_type'].' '.(string)$r['unit_number'])) ?></td>
            <td><?= htmlspecialchars((string)$r['tenant_name']) ?></td>
            <td><?= htmlspecialchars((string)$r['tenant_phone']) ?></td>
            <td><?= htmlspecialchars((string)$r['start_date']) ?></td>
            <td><?= htmlspecialchars((string)$r['end_date']) ?></td>
            <td><?= htmlspecialchars((string)$r['status']) ?></td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>