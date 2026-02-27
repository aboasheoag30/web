<?php
declare(strict_types=1);
$active='reports';
$title='التقارير';
require __DIR__.'/_layout/boot.php';
require __DIR__.'/_layout/header.php';
require __DIR__.'/_layout/sidebar.php';
?>

<div class="section-title">
  <div>
    <h2>التقارير</h2>
    <div class="small">تقرير ملخص + تقرير العقود.</div>
  </div>
</div>

<div class="grid cols2">
  <div class="card">
    <h3 style="margin:0 0 10px">ملخص مالي</h3>
    <label>العقار</label>
    <select class="input" id="prop"></select>
    <div class="row" style="justify-content:flex-start; gap:10px; margin-top:10px">
      <button class="btn primary" onclick="loadSummary()">عرض</button>
    </div>
    <div id="out" style="margin-top:12px"></div>
  </div>

  <div class="card">
    <h3 style="margin:0 0 10px">تقرير العقود</h3>
    <div class="small">افتح صفحة العقود ثم صدّر PDF/Excel من داخل العقد.</div>
    <button class="btn primary" style="margin-top:10px" onclick="location.href='contracts.php'">فتح صفحة العقود</button>
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

async function loadSummary(){
  out.innerHTML = "";
  const pid = prop.value ? ("?propertyId="+prop.value) : "";
  const r = await api("/admin/reports_summary.php"+pid);
  const s = r.summary || {};
  out.innerHTML = `
    <div class="grid cols3">
      <div class="card kpi"><div class="small">المستحق</div><div class="v">${fmt(s.totalDue)} ريال</div></div>
      <div class="card kpi"><div class="small">المسدد</div><div class="v">${fmt(s.totalPaid)} ريال</div></div>
      <div class="card kpi"><div class="small">المتبقي</div><div class="v">${fmt(s.totalRemaining)} ريال</div></div>
    </div>
    <div class="small" style="margin-top:10px">الوحدات: ${s.unitsCount||0} — المستأجرون: ${s.tenantsCount||0}</div>
  `;
}

loadProps().then(loadSummary);
</script>

<?php require __DIR__.'/_layout/footer.php'; ?>
