<?php // owner/_layout/sidebar.php ?>
<?php
  $items = [
    ['key'=>'dashboard','label'=>'الرئيسية','href'=>'dashboard.php'],
    ['key'=>'properties','label'=>'العقارات والوحدات','href'=>'properties.php'],
    ['key'=>'contracts','label'=>'العقود','href'=>'contracts.php'],
    ['key'=>'finance','label'=>'المالية','href'=>'finance.php'],
    ['key'=>'reports','label'=>'التقارير','href'=>'reports.php'],
    ['key'=>'tenants','label'=>'المستأجرين','href'=>'tenants.php'],
    ['key'=>'staff','label'=>'المستخدمون','href'=>'staff.php'],
    ['key'=>'inbox','label'=>'الرسائل والتنبيهات','href'=>'inbox.php','badge'=>'nav_notifs'],
  ];
?>
  <nav class="nav">
    <?php foreach($items as $it): ?>
      <a data-close-drawer="1"
         href="<?= htmlspecialchars($it['href']) ?>"
         class="<?= ($active===$it['key'])?'active':'' ?>">
        <span><?= htmlspecialchars($it['label']) ?></span>
        <?php if(!empty($it['badge'])): ?>
          <span class="badge" id="<?= htmlspecialchars($it['badge']) ?>">0</span>
        <?php endif; ?>
      </a>
    <?php endforeach; ?>
  </nav>
