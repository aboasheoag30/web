<?php
declare(strict_types=1);
$active='inbox';
$title='الرسائل والتنبيهات';
require __DIR__.'/_layout/boot.php';
require __DIR__.'/_layout/header.php';
require __DIR__.'/_layout/sidebar.php';
?>

<div class="section-title">
  <div>
    <h2>الرسائل والتنبيهات</h2>
    <div class="small">صندوق التنبيهات داخل النظام.</div>
  </div>
  <button class="btn primary" onclick="load()">تحديث</button>
</div>

<div class="card">
  <div id="list"></div>
</div>

<script>
async function load(){
  list.innerHTML="";
  const n = await api("/notifications/inbox.php");
  const items = n.items || [];
  if(!items.length){
    list.innerHTML = `<div class="empty">لا توجد رسائل</div>`;
    return;
  }
  for(const it of items){
    const div=document.createElement("div");
    div.style.padding="12px 0";
    div.style.borderBottom="1px dashed rgba(15,23,42,.18)";
    div.innerHTML = `
      <div style="display:flex;justify-content:space-between;gap:10px;align-items:flex-start">
        <b>${it.title||"تنبيه"}</b>
        <span class="small">${it.created_at||""}</span>
      </div>
      <div style="margin-top:8px">${it.body||""}</div>
    `;
    list.appendChild(div);
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
