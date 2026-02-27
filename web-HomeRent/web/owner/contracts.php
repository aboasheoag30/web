<?php
declare(strict_types=1);
$active='contracts';
$title='العقود';
require __DIR__.'/_layout/boot.php';
require __DIR__.'/_layout/header.php';
require __DIR__.'/_layout/sidebar.php';
?>

<div class="section-title">
  <div>
    <h2>العقود</h2>
    <div class="small">بحث سريع وعرض قائمة العقود ثم فتح تقرير العقد.</div>
  </div>
  <div class="row" style="justify-content:flex-start; gap:10px">
    <button class="btn primary" onclick="location.href='contract_add.php'">إضافة عقد</button>
  </div>
</div>

<div class="card">
  <div class="row">
    <div>
      <label>بحث</label>
      <input id="q" class="input" placeholder="رقم العقد / المستأجر / العقار / الوحدة"/>
    </div>
    <div>
      <label>الحالة</label>
      <select id="status" class="input">
        <option value="">كل الحالات</option>
        <option value="active">فعّال</option>
        <option value="ended">منتهي</option>
        <option value="canceled">ملغي</option>
      </select>
    </div>
    <div style="display:flex;gap:10px;align-items:end">
      <button class="btn primary" onclick="load()">بحث</button>
      <button class="btn" onclick="clearFilters()">مسح</button>
    </div>
  </div>
</div>

<div class="card" style="margin-top:12px">
  <div class="section-title">
    <div>
      <h3 style="margin:0">القائمة</h3>
      <div class="small" id="meta">—</div>
    </div>
  </div>

  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>#</th><th>رقم العقد</th><th>العقار</th><th>الوحدة</th>
          <th>المستأجر</th><th>الجوال</th><th>بداية</th><th>نهاية</th><th>الحالة</th><th>فتح</th>
        </tr>
      </thead>
      <tbody id="rows"></tbody>
    </table>
  </div>
</div>

<script>
function sBadge(st){
  if(st==="active") return '<span class="s-badge ok">فعّال</span>';
  if(st==="ended") return '<span class="s-badge gray">منتهي</span>';
  if(st==="canceled") return '<span class="s-badge bad">ملغي</span>';
  return '<span class="s-badge warn">'+(st||'—')+'</span>';
}

function clearFilters(){ q.value=""; status.value=""; load(); }

async function load(){
  rows.innerHTML = "";
  meta.textContent = "—";

  const qs = new URLSearchParams();
  if((q.value||"").trim()) qs.set("q", (q.value||"").trim());
  if(status.value) qs.set("status", status.value);

  const r = await api("/admin/contracts_list.php?"+qs.toString());
  const items = r.items || [];
  meta.textContent = "عدد العقود: " + items.length;

  if(!items.length){
    rows.innerHTML = `<tr><td colspan="10" class="empty">لا توجد نتائج</td></tr>`;
    return;
  }

  for(const it of items){
    const tr=document.createElement("tr");
    tr.innerHTML = `
      <td>${it.id}</td>
      <td><b>${it.contract_number || '-'}</b></td>
      <td>${it.property_name || '-'}</td>
      <td>${(it.unit_type||'')+' '+(it.unit_name||'')}</td>
      <td>${it.tenant_name || '-'}</td>
      <td>${it.tenant_phone || '-'}</td>
      <td>${it.start_date || '-'}</td>
      <td>${it.end_date || '-'}</td>
      <td>${sBadge(it.status)}</td>
      <td><button class="btn" onclick="location.href='contract.php?id=${it.id}'">فتح</button></td>
    `;
    rows.appendChild(tr);
  }
}

async function boot(){
  try{
    const me = await api("/me.php");
    if(!(["owner","staff"]).includes(me.me.role)) { alert("ليس لديك صلاحية"); logout(); return; }
    await load();
  }catch(e){ alert(e.message||"تعذر التحميل"); logout(); }
}
boot();
</script>

<?php require __DIR__.'/_layout/footer.php'; ?>
