<?php
$ctx = must_be_site_admin_or_staff();
$pdo = db();
// ====== إحصائيات المستخدمين ======
$usersTotal = (int)$pdo->query("SELECT COUNT(*) c FROM users")->fetch()['c'];
$owners     = (int)$pdo->query("SELECT COUNT(*) c FROM users WHERE role='owner'")->fetch()['c'];
$staff      = (int)$pdo->query("SELECT COUNT(*) c FROM users WHERE role='staff'")->fetch()['c'];
$tenants    = (int)$pdo->query("SELECT COUNT(*) c FROM users WHERE role='tenant'")->fetch()['c'];
$admins     = (int)$pdo->query("SELECT COUNT(*) c FROM users WHERE role='site_admin'")->fetch()['c'];
$adminStaff = (int)$pdo->query("SELECT COUNT(*) c FROM users WHERE role='site_admin_staff'")->fetch()['c'];
$disabled   = (int)$pdo->query("SELECT COUNT(*) c FROM users WHERE status='disabled'")->fetch()['c'];

// ====== إحصائيات العقارات/الوحدات/العقود ======
$properties = (int)$pdo->query("SELECT COUNT(*) c FROM properties")->fetch()['c'];
$units      = (int)$pdo->query("SELECT COUNT(*) c FROM units")->fetch()['c'];

$contractsAll    = (int)$pdo->query("SELECT COUNT(*) c FROM contracts")->fetch()['c'];
$contractsActive = (int)$pdo->query("SELECT COUNT(*) c FROM contracts WHERE status='active'")->fetch()['c'];
$contractsEnded  = (int)$pdo->query("SELECT COUNT(*) c FROM contracts WHERE status='ended'")->fetch()['c'];

// ====== مدفوعات/مواعيد (تقريبي حسب جدول schedules) ======
// متأخرات: due_date < اليوم و status != paid
$overdue = (int)$pdo->query("
  SELECT COUNT(*) c
  FROM contract_schedules
  WHERE status <> 'paid' AND due_date < CURDATE()
")->fetch()['c'];

// تستحق خلال 30 يوم
$due30 = (int)$pdo->query("
  SELECT COUNT(*) c
  FROM contract_schedules
  WHERE status <> 'paid' AND due_date >= CURDATE() AND due_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)
")->fetch()['c'];

// عقود تنتهي خلال 60 يوم:
// end_date = start_date + months - 1 day
$ending60 = (int)$pdo->query("
  SELECT COUNT(*) c
  FROM contracts
  WHERE status='active'
    AND DATE_ADD(start_date, INTERVAL months MONTH) <= DATE_ADD(CURDATE(), INTERVAL 60 DAY)
")->fetch()['c'];

// ====== أحدث المستخدمين ======
$latestUsers = $pdo->query("
  SELECT id, full_name, email, phone, role, status, created_at
  FROM users
  ORDER BY id DESC
  LIMIT 8
")->fetchAll();

// ====== أحدث الرسائل المرسلة ======
$latestMsgs = $pdo->query("
  SELECT m.id, m.title, m.target_type, m.created_at,
         (SELECT COUNT(*) FROM admin_message_recipients r WHERE r.message_id=m.id) recipients_count
  FROM admin_messages m
  ORDER BY m.id DESC
  LIMIT 6
")->fetchAll();

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

<div class="grid cols3">
  <div class="card">
    <div class="small">عدد المستخدمين</div>
    <div style="font-size:26px;font-weight:900"><?= $usersTotal ?></div>
    <div class="small">موقوفين: <b><?= $disabled ?></b></div>
  </div>

  <div class="card">
    <div class="small">ملاك العقار</div>
    <div style="font-size:26px;font-weight:900"><?= $owners ?></div>
    <div class="small">تابعين الملاك: <b><?= $staff ?></b></div>
  </div>

  <div class="card">
    <div class="small">مستأجرين</div>
    <div style="font-size:26px;font-weight:900"><?= $tenants ?></div>
    <div class="small">مدير/مسؤولي نظام: <b><?= $admins + $adminStaff ?></b></div>
  </div>

  <div class="card">
    <div class="small">العقارات</div>
    <div style="font-size:26px;font-weight:900"><?= $properties ?></div>
    <div class="small">الوحدات: <b><?= $units ?></b></div>
  </div>

  <div class="card">
    <div class="small">العقود</div>
    <div style="font-size:26px;font-weight:900"><?= $contractsAll ?></div>
    <div class="small">نشطة: <b><?= $contractsActive ?></b> — منتهية: <b><?= $contractsEnded ?></b></div>
  </div>

  <div class="card">
    <div class="small">تنبيهات سريعة</div>
    <div class="row" style="margin-top:6px;gap:8px">
      <span class="badge danger">متأخرات: <?= $overdue ?></span>
      <span class="badge warn">تستحق ≤ 30 يوم: <?= $due30 ?></span>
      <span class="badge ok">تنتهي ≤ 60 يوم: <?= $ending60 ?></span>
    </div>
    <div class="small" style="margin-top:8px">
      * تعتمد على <b>contract_schedules</b> ومدة العقود.
    </div>
  </div>
</div>

<div class="grid" style="grid-template-columns:1fr 1fr;gap:12px;margin-top:12px">
  <div class="card">
    <div class="row" style="justify-content:space-between">
      <div style="font-weight:900">أحدث المستخدمين</div>
      <a class="small" href="index.php?page=users">عرض الجميع</a>
    </div>

    <table class="table" style="margin-top:10px">
      <thead>
        <tr>
          <th>المستخدم</th>
          <th>الدور</th>
          <th>الحالة</th>
          <th>تاريخ</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach($latestUsers as $u): ?>
        <tr>
          <td>
            <b><?= htmlspecialchars((string)$u['full_name']) ?></b>
            <div class="small"><?= htmlspecialchars((string)$u['email']) ?> — <?= htmlspecialchars((string)($u['phone'] ?? '—')) ?></div>
          </td>
          <td><?= htmlspecialchars((string)$u['role']) ?></td>
          <td>
            <?php if(($u['status'] ?? '')==='active'): ?>
              <span class="badge ok">نشط</span>
            <?php else: ?>
              <span class="badge danger">موقوف</span>
            <?php endif; ?>
          </td>
          <td class="small"><?= htmlspecialchars((string)$u['created_at']) ?></td>
        </tr>
      <?php endforeach; ?>
      <?php if(!$latestUsers): ?>
        <tr><td colspan="4" class="small">لا يوجد</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>

  <div class="card">
    <div class="row" style="justify-content:space-between">
      <div style="font-weight:900">أحدث الرسائل المرسلة</div>
      <a class="small" href="index.php?page=messages">السجل</a>
    </div>

    <table class="table" style="margin-top:10px">
      <thead>
        <tr>
          <th>العنوان</th>
          <th>الاستهداف</th>
          <th>المستلمون</th>
          <th>التاريخ</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach($latestMsgs as $m): ?>
        <tr>
          <td><b><?= htmlspecialchars((string)$m['title']) ?></b></td>
          <td><?= htmlspecialchars(target_label((string)$m['target_type'])) ?></td>
          <td><span class="badge"><?= (int)$m['recipients_count'] ?></span></td>
          <td class="small"><?= htmlspecialchars((string)$m['created_at']) ?></td>
        </tr>
      <?php endforeach; ?>
      <?php if(!$latestMsgs): ?>
        <tr><td colspan="4" class="small">لا يوجد رسائل</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>