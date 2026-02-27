<?php
declare(strict_types=1);
$active='tenants';
$title='المستأجرين';
require __DIR__.'/_layout/boot.php';
require __DIR__.'/_layout/header.php';
require __DIR__.'/_layout/sidebar.php';
?>

<div class="section-title">
  <div>
    <h2>المستأجرين</h2>
    <div class="small">قائمة المستأجرين والبحث السريع.</div>
  </div>
</div>

<div class="card">
  <div class="row">
    <div>
      <label>بحث</label>
      <input id="q" class="input" placeholder="اسم / جوال / بريد"/>
    </div>
    <div style="display:flex;gap:10px;align-items:end">
      <button class="btn primary" onclick="load()">بحث</button>
      <button class="btn" onclick="q.value='';load()">مسح</button>
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
      <thead><tr><th>#</th><th>الاسم</th><th>الجوال</th><th>البريد</th><th>عدد العقود</th></tr></thead>
      <tbody id="rows"></tbody>
    </table>
  </div>
</div>

<script>
async function load(){
  rows.innerHTML="";
  meta.textContent="—";
  const qs = new URLSearchParams();
  if((q.value||"").trim()) qs.set("q",(q.value||"").trim());

  const r = await api("/admin/tenants_list.php?"+qs.toString());
  const items = r.items || [];
  meta.textContent = "عدد المستأجرين: " + items.length;

  if(!items.length){
    rows.innerHTML = `<tr><td colspan="5" class="empty">لا توجد نتائج</td></tr>`;
    return;
  }
  for(const it of items){
    const tr=document.createElement("tr");
    tr.innerHTML = `
      <td>${it.id||""}</td>
      <td><b>${it.full_name||it.name||"-"}</b></td>
      <td>${it.phone||"-"}</td>
      <td>${it.email||"-"}</td>
      <td>${it.contracts_count ?? "-"}</td>
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
