<?php
declare(strict_types=1);
$active='staff';
$title='المستخدمون';
require __DIR__.'/_layout/boot.php';
require __DIR__.'/_layout/header.php';
require __DIR__.'/_layout/sidebar.php';
?>

<div class="section-title">
  <div>
    <h2>المستخدمون التابعون للمالك</h2>
    <div class="small">إضافة مستخدم جديد (Staff) وتعديل بريده.</div>
  </div>
</div>

<div class="grid cols2">
  <div class="card">
    <h3 style="margin:0 0 10px">إضافة مستخدم</h3>
    <label>الاسم</label>
    <input class="input" id="s_name" placeholder="اسم المستخدم"/>
    <label>البريد</label>
    <input class="input" id="s_email" placeholder="email@example.com"/>
    <label>الجوال (اختياري)</label>
    <input class="input" id="s_phone" placeholder="05xxxxxxxx"/>
    <div class="row" style="justify-content:flex-start; gap:10px; margin-top:10px">
      <button class="btn primary" onclick="addStaff()">إضافة</button>
    </div>
    <div id="msg" class="small" style="margin-top:10px"></div>
  </div>

  <div class="card">
    <div class="section-title">
      <div>
        <h3 style="margin:0">قائمة المستخدمين</h3>
        <div class="small">سيصل بريد بكلمة المرور عند الإضافة.</div>
      </div>
      <div class="row" style="justify-content:flex-start">
        <button class="btn" onclick="load()">تحديث</button>
      </div>
    </div>

    <div class="table-wrap">
      <table>
        <thead><tr><th>#</th><th>الاسم</th><th>البريد</th><th>الجوال</th><th>الحالة</th><th>إجراء</th></tr></thead>
        <tbody id="rows"></tbody>
      </table>
    </div>
  </div>
</div>

<script>
function esc(s){ return (s??"").toString().replace(/[&<>"]/g, m=>({ "&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;" }[m])); }

async function load(){
  rows.innerHTML = "";
  const r = await api("/admin/staff_list.php");
  const items = r.items||[];
  if(!items.length){
    rows.innerHTML = `<tr><td colspan="6" class="empty">لا يوجد مستخدمون</td></tr>`;
    return;
  }
  for(const it of items){
    rows.innerHTML += `<tr>
      <td>${it.id}</td>
      <td>${esc(it.full_name)}</td>
      <td>${esc(it.email)}</td>
      <td>${esc(it.phone||"")}</td>
      <td>${esc(it.status||"active")}</td>
      <td><button class="btn mini" onclick="editEmail(${it.id}, '${(it.email||'').replace(/'/g,'\'')}')">تعديل البريد</button></td>
    </tr>`;
  }
}

async function addStaff(){
  msg.textContent = "";
  try{
    const r = await api("/admin/staff_create.php",{method:"POST", body:{fullName:s_name.value, email:s_email.value, phone:s_phone.value}});
    msg.textContent = "✅ تم إضافة المستخدم رقم: "+r.staffUserId;
    s_name.value=""; s_email.value=""; s_phone.value="";
    await load();
  }catch(e){
    msg.textContent = "❌ "+e.message;
  }
}

async function editEmail(staffId, currentEmail){
  const email = prompt("أدخل البريد الجديد:", currentEmail||"");
  if(!email) return;
  try{
    await api("/admin/staff_update.php",{method:"POST", body:{staffId:staffId, email:email}});
    ownerToast("✅ تم تحديث البريد", "ok");
    await load();
  }catch(e){ ownerToast("❌ "+e.message, "err"); }
}

load();
</script>

<?php require __DIR__.'/_layout/footer.php'; ?>
