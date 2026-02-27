<?php
declare(strict_types=1);
$active='dashboard';
$title='الرئيسية';
require __DIR__.'/_layout/boot.php';
require __DIR__.'/_layout/header.php';
require __DIR__.'/_layout/sidebar.php';
?>

<div class="section-title">
  <div>
    <h2>لوحة المعلومات</h2>
    <div class="small">ملخص سريع للأرقام + الطلبات والتنبيهات</div>
  </div>
  <div class="row" style="justify-content:flex-start">
    <button class="btn" onclick="location.href='reports.html'">التقارير</button>
    <button class="btn primary" onclick="refreshAll()">تحديث</button>
  </div>
</div>

<div class="grid cols6">
  <div class="card kpi"><div class="label">العقارات</div><div id="st_props" class="value">—</div></div>
  <div class="card kpi"><div class="label">الوحدات المؤجرة</div><div id="st_rented" class="value">—</div></div>
  <div class="card kpi"><div class="label">المستأجرون</div><div id="st_tenants" class="value">—</div></div>
  <div class="card kpi"><div class="label">المستخدمون المضافون</div><div id="st_staff" class="value">—</div></div>
  <div class="card kpi"><div class="label">طلبات معلّقة</div><div id="st_pending" class="value">—</div></div>
  <div class="card kpi"><div class="label">إجمالي المسدد</div><div id="st_paid" class="value">—</div></div>
</div>

<div class="grid cols2" style="margin-top:12px">
  <div class="card">
    <div class="section-title">
      <div>
        <h3>طلبات السداد المعلقة</h3>
        <div class="small">اعتماد/رفض طلبات الإيصالات المرفوعة من المستأجرين.</div>
      </div>
      <button class="btn" onclick="loadRequests()">تحديث</button>
    </div>

    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>المستأجر</th><th>العقد</th><th>الدفعة</th><th>المبلغ</th><th>الملف</th><th>إجراء</th>
          </tr>
        </thead>
        <tbody id="reqs"></tbody>
      </table>
    </div>
  </div>

  <div class="card">
    <div class="section-title">
      <div>
        <h3>آخر التنبيهات</h3>
        <div class="small">التنبيهات داخل النظام.</div>
      </div>
      <button class="btn" onclick="loadNotifs()">تحديث</button>
    </div>

    <div id="notifs"></div>
  </div>
</div>

<script>
function fmt(n){ return (Number(n||0)).toFixed(2); }

async function loadDashboard(){
  const d = await api("/admin/dashboard.php");
  st_props.textContent   = d.stats.properties ?? "0";
  st_rented.textContent  = d.stats.rentedUnits ?? "0";
  st_tenants.textContent = d.stats.tenants ?? "0";
  st_staff.textContent   = d.stats.staffUsers ?? "0";
  st_pending.textContent = d.stats.pendingPaymentRequests ?? "0";
  st_paid.textContent    = fmt(d.stats.totalPaid) + " ريال";
}

async function loadRequests(){
  const body = document.getElementById("reqs");
  body.innerHTML = "";
  const r = await api("/admin/payment_requests_list.php?status=pending");
  const items = r.items || [];
  if(!items.length){
    body.innerHTML = `<tr><td colspan="6" class="empty">لا توجد طلبات معلّقة</td></tr>`;
    return;
  }
  for(const it of items){
    const fileLink = it.file_path ? `<a href="../${it.file_path}" target="_blank">${it.kind||"ملف"}</a>` : "-";
    const tr = document.createElement("tr");
    tr.innerHTML = `
      <td>${it.tenant_name||"-"}</td>
      <td>${it.contract_number||"-"}</td>
      <td>#${it.seq} (${it.due_date})</td>
      <td>${fmt(it.amount)}</td>
      <td>${fileLink}</td>
      <td>
        <div class="actions">
          <button class="btn primary" onclick="approve(${it.id})">اعتماد</button>
          <button class="btn danger" onclick="reject(${it.id})">رفض</button>
        </div>
      </td>`;
    body.appendChild(tr);
  }
}

async function approve(id){
  if(!confirm("اعتماد الطلب؟")) return;
  const r=await api("/admin/payment_request_approve.php",{method:"POST",body:{requestId:id}});
  if(r.receiptUrl) window.open(r.receiptUrl,"_blank");
  ownerToast("تم اعتماد الطلب", "ok");
  await refreshAll();
}
async function reject(id){
  const notes = prompt("سبب الرفض (اختياري):") || "";
  await api("/admin/payment_request_reject.php",{method:"POST",body:{requestId:id,notes}});
  ownerToast("تم رفض الطلب", "ok");
  await refreshAll();
}

async function loadNotifs(){
  const wrap=document.getElementById("notifs");
  wrap.innerHTML="";
  const n=await api("/notifications/inbox.php");
  const items=n.items||[];
  if(!items.length){
    wrap.innerHTML = `<div class="empty">لا توجد تنبيهات</div>`;
    return;
  }
  for(const it of items.slice(0,12)){
    const div=document.createElement("div");
    div.style.padding="10px 0";
    div.style.borderBottom="1px dashed rgba(15,23,42,.18)";
    div.innerHTML = `
      <div style="display:flex;justify-content:space-between;gap:10px;align-items:flex-start">
        <b>${it.title||"تنبيه"}</b>
        <span class="small">${it.created_at||""}</span>
      </div>
      <div style="margin-top:6px">${it.body||""}</div>`;
    wrap.appendChild(div);
  }
}

async function refreshAll(){
  await Promise.allSettled([loadDashboard(), loadRequests(), loadNotifs()]);
}

async function boot(){
  try{
    const me = await api("/me.php");
    if(!(["owner","staff"]).includes(me.me.role)) { alert("ليس لديك صلاحية"); logout(); return; }
    await refreshAll();
  }catch(e){
    alert(e.message||"تعذر تحميل البيانات");
    logout();
  }
}
boot();
</script>

<?php require __DIR__.'/_layout/footer.php'; ?>
