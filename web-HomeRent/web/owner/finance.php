<?php
declare(strict_types=1);
$active='finance';
$title='المالية';
require __DIR__.'/_layout/boot.php';
require __DIR__.'/_layout/header.php';
require __DIR__.'/_layout/sidebar.php';
?>

<div class="section-title">
  <div>
    <h2>المالية</h2>
    <div class="small">ملخص المستحق/المسدد/المتبقي حسب عقار أو لجميع العقارات.</div>
  </div>
</div>

<div class="grid cols2">
  <div class="card">
    <h3 style="margin:0 0 10px">فلترة</h3>
    <label>العقار</label>
    <select class="input" id="prop"></select>
    <div class="row" style="justify-content:flex-start; gap:10px; margin-top:10px">
      <button class="btn primary" onclick="load()">تحديث</button>
    </div>
  </div>

  <div class="card">
    <h3 style="margin:0 0 10px">الملخص</h3>
    <div class="grid cols3">
      <div class="card kpi"><div class="small">إجمالي المستحق</div><div class="v" id="k_due">—</div></div>
      <div class="card kpi"><div class="small">إجمالي المسدد</div><div class="v" id="k_paid">—</div></div>
      <div class="card kpi"><div class="small">المتبقي</div><div class="v" id="k_rem">—</div></div>
    </div>
    <div class="small" id="k_meta" style="margin-top:10px">—</div>
  </div>
</div>

<script>
function fmt(n){ return Number(n||0).toFixed(2); }

async function loadProps(){
  const p = await api("/admin/properties_list.php");
  prop.innerHTML = "";
  const opt0=document.createElement("option");
  opt0.value=""; opt0.textContent="جميع العقارات";
  prop.appendChild(opt0);
  for(const it of (p.items||[])){
    const opt=document.createElement("option");
    opt.value=it.id; opt.textContent=it.name;
    prop.appendChild(opt);
  }
}

async function load(){
  const pid = prop.value ? ("?propertyId="+prop.value) : "";
  const r = await api("/admin/reports_summary.php"+pid);
  const s = r.summary || {};
  k_due.textContent = fmt(s.totalDue) + " ريال";
  k_paid.textContent = fmt(s.totalPaid) + " ريال";
  k_rem.textContent = fmt(s.totalRemaining) + " ريال";
  k_meta.textContent = "الوحدات: " + (s.unitsCount||0) + " — المستأجرون: " + (s.tenantsCount||0);
}
loadProps().then(load);
</script>

<?php require __DIR__.'/_layout/footer.php'; ?>
