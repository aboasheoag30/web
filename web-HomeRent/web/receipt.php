<?php
declare(strict_types=1);
require_once __DIR__ . '/../api/_core/db.php';
require_once __DIR__ . '/../api/_core/helpers.php';
require_once __DIR__ . '/../api/_core/config.php';

$paymentId = safe_int($_GET['paymentId'] ?? 0);
if ($paymentId <= 0) { http_response_code(400); echo "paymentId required"; exit; }

$pdo = db();
$stmt = $pdo->prepare("SELECT * FROM v_payments WHERE payment_id=? LIMIT 1");
$stmt->execute([$paymentId]);
$p = $stmt->fetch();
if (!$p) { http_response_code(404); echo "Not found"; exit; }
?>
<!doctype html>
<html lang="ar" dir="rtl"><head>
<meta charset="utf-8"/><meta name="viewport" content="width=device-width,initial-scale=1"/>
<title>سند سداد - <?= htmlspecialchars(APP_NAME) ?></title>
<style>
body{font-family:Tahoma,Arial;margin:24px;background:#f6f7f9}
.card{max-width:720px;margin:auto;background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:18px}
h1{margin:0 0 12px;font-size:22px}
.meta{display:flex;gap:12px;flex-wrap:wrap;color:#374151}
.row{display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px dashed #e5e7eb}
.row:last-child{border-bottom:none}
.label{color:#6b7280}.value{font-weight:700}
.btns{display:flex;gap:10px;justify-content:flex-start;margin-top:14px}
button{border:1px solid #111827;background:#111827;color:#fff;padding:10px 14px;border-radius:10px;cursor:pointer}
@media print{body{background:#fff;margin:0}.btns{display:none}.card{border:none}}
</style></head><body>
<div class="card">
  <h1>سند سداد</h1>
  <div class="meta">
    <div>النظام: <b><?= htmlspecialchars(APP_NAME) ?></b></div>
    <div>رقم السند: <b><?= (int)$p['payment_id'] ?></b></div>
    <div>التاريخ: <b><?= htmlspecialchars($p['paid_at']) ?></b></div>
    <div>المصدر: <b><?= $p['source']==='manual' ? 'سداد يدوي من المالك' : 'اعتماد طلب سداد' ?></b></div>
  </div>
  <div style="margin-top:14px">
    <div class="row"><div class="label">اسم المستأجر</div><div class="value"><?= htmlspecialchars($p['tenant_name']) ?></div></div>
    <div class="row"><div class="label">العقار</div><div class="value"><?= htmlspecialchars($p['property_name']) ?></div></div>
    <div class="row"><div class="label">الوحدة</div><div class="value"><?= htmlspecialchars($p['unit_type']) ?> <?= htmlspecialchars($p['unit_name']) ?></div></div>
    <div class="row"><div class="label">رقم العقد</div><div class="value"><?= htmlspecialchars($p['contract_number']) ?></div></div>
    <div class="row"><div class="label">الدفعة/القسط</div><div class="value">#<?= (int)$p['seq'] ?> (استحقاق: <?= htmlspecialchars($p['due_date']) ?>)</div></div>
    <div class="row"><div class="label">المبلغ</div><div class="value"><?= htmlspecialchars(number_format((float)$p['amount'],2)) ?> ريال</div></div>
  </div>
  <div class="btns">
    <button onclick="window.print()">طباعة / حفظ PDF</button>
    <button onclick="window.location.href='<?= htmlspecialchars(BASE_URL) ?>/web/'">رجوع</button>
  </div>
</div>
</body></html>
