<?php
declare(strict_types=1);
$active='contracts';
$title='تفاصيل العقد';
require __DIR__.'/_layout/boot.php';
require __DIR__.'/_layout/header.php';
require __DIR__.'/_layout/sidebar.php';
?>

<div class="section-title">
  <div>
    <h2 id="titleH">تفاصيل العقد</h2>
    <div class="small" id="subH">—</div>
  </div>
  <div class="row" style="justify-content:flex-start; gap:10px">
    <button class="btn" onclick="location.href='contracts.php'">رجوع</button>
    <button class="btn" onclick="window.print()">طباعة</button>
    <button class="btn" onclick="exportPDF()">تصدير PDF</button>
    <button class="btn" onclick="exportExcel()">تصدير Excel</button>
    <button class="btn primary" onclick="load()">تحديث</button>
  </div>
</div>

<div class="grid cols5">
  <div class="card kpi"><div class="small">مسدد</div><div class="v" id="k_paid">—</div></div>
  <div class="card kpi"><div class="small">متبقي</div><div class="v" id="k_rem">—</div></div>
  <div class="card kpi"><div class="small">متأخرات</div><div class="v" id="k_over">—</div></div>
  <div class="card kpi"><div class="small">مدة متبقية</div><div class="v" id="k_days">—</div></div>
  <div class="card kpi"><div class="small">حالة العقد</div><div class="v" id="k_status">—</div></div>
</div>

<div class="grid cols2" style="margin-top:12px">
  <div class="card">
    <h3 style="margin:0 0 10px">بيانات العقد</h3>
    <div class="table-wrap"><table><tbody id="info_contract"></tbody></table></div>

    <h3 style="margin:16px 0 10px">بيانات الوحدة</h3>
    <div class="table-wrap"><table><tbody id="info_unit"></tbody></table></div>

    <h3 style="margin:16px 0 10px">بيانات المستأجر</h3>
    <div class="table-wrap"><table><tbody id="info_tenant"></tbody></table></div>

    <h3 style="margin:16px 0 10px">البيانات المالية</h3>
    <div class="table-wrap"><table><tbody id="info_fin"></tbody></table></div>
  </div>

  <div class="card">
    <div class="section-title">
      <div>
        <h3 style="margin:0">المدفوعات</h3>
        <div class="small">سجل آخر الدفعات.</div>
      </div>
    </div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>التاريخ</th><th>المبلغ</th><th>المصدر</th><th>سند</th></tr></thead>
        <tbody id="payRows"></tbody>
      </table>
    </div>

    <div class="notice" style="margin-top:10px">
      يمكنك إضافة سداد لكل استحقاق من جدول السداد (بالأسفل) وسيصدر سند تلقائيًا.
    </div>
  </div>
</div>

<div class="card" style="margin-top:12px">
  <div class="section-title">
    <div>
      <h3 style="margin:0">جدول السداد</h3>
      <div class="small">الحالة + المدة المتبقية لكل استحقاق.</div>
    </div>
  </div>

  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>#</th><th>موعد الاستحقاق</th><th>المبلغ</th><th>الحالة</th><th>تاريخ السداد</th><th>متبقي/يوم</th><th>إجراء</th>
        </tr>
      </thead>
      <tbody id="schRows"></tbody>
    </table>
  </div>
</div>

<script>
const id = new URLSearchParams(location.search).get("id");
function fmt(n){ return (Number(n||0)).toFixed(2); }

function badgeStatus(st){
  if(st==='paid') return '<span class="badge green">مدفوع</span>';
  if(st==='pending') return '<span class="badge orange">بانتظار الاعتماد</span>';
  return '<span class="badge red">غير مدفوع</span>';
}
function badgeContract(st){
  if(st==='active') return '<span class="s-badge ok">فعّال</span>';
  if(st==='ended') return '<span class="s-badge gray">منتهي</span>';
  if(st==='canceled') return '<span class="s-badge bad">ملغي</span>';
  return '<span class="s-badge warn">'+(st||"—")+'</span>';
}
function daysBadge(days){
  const d = Number(days||0);
  if(d <= 0) return '<span class="s-badge bad">'+d+' يوم</span>';
  if(d <= 60) return '<span class="s-badge warn">'+d+' يوم</span>';
  return '<span class="s-badge ok">'+d+' يوم</span>';
}
function schDaysBadge(days){
  const d = Number(days||0);
  if(d < 0) return '<span class="s-badge bad">'+d+' يوم</span>';
  if(d <= 7) return '<span class="s-badge warn">'+d+' يوم</span>';
  return '<span class="s-badge gray">'+d+' يوم</span>';
}

async function paySchedule(scheduleId, amount){
  const v = prompt("اكتب مبلغ السداد:", String(amount||0));
  if(v===null) return;
  const amt = Number(v||0);
  if(!amt || amt<=0){ alert("مبلغ غير صحيح"); return; }
  if(!confirm("تأكيد إضافة سداد؟ سيتم إصدار سند.")) return;

  const r = await api("/admin/manual_payment_add.php",{method:"POST", body:{scheduleId:Number(scheduleId), amount:amt}});
  window.open(r.receiptUrl, "_blank");
  await load();
}

async function load(){
  if(!id){ alert("معرف العقد غير موجود"); location.href="contracts.php"; return; }
  const r = await api("/admin/contract_get.php?id="+id);
  const c = r.contract || {};
  const sch = r.schedules || [];
  const pays = r.payments || [];
  const s = r.summary || {};

  titleH.textContent = "عقد رقم: " + (c.contract_number||"—");
  subH.textContent = (c.property_name||"") + " — " + ((c.unit_type||"")+" "+(c.unit_name||"")) + " — المستأجر: " + (c.tenant_name||"—");

  k_paid.textContent = fmt(s.paid) + " ريال";
  k_rem.textContent  = fmt(s.remaining) + " ريال";
  k_over.textContent = fmt(s.overdue) + " ريال";
  k_days.innerHTML   = daysBadge(c.days_to_end);
  k_status.innerHTML = badgeContract(c.status);

  // معلومات العقد
  const contractRows = [
    ["رقم العقد", c.contract_number],
    ["بداية العقد", c.start_date],
    ["نهاية العقد", c.end_date + " ("+ (c.days_to_end??"—") +" يوم)"],
    ["مدة العقد", (c.months||"—") + " شهر"],
  ];
  info_contract.innerHTML = contractRows.map(([k,v])=>`<tr><td style="width:40%"><b>${k}</b></td><td>${v||"—"}</td></tr>`).join("");

  // بيانات الوحدة
  const unitRows = [
    ["العقار", c.property_name],
    ["المدينة", c.city || "—"],
    ["الحي", c.district || "—"],
    ["نوع/رقم الوحدة", (c.unit_type||"—")+" "+(c.unit_name||"")],
  ];
  info_unit.innerHTML = unitRows.map(([k,v])=>`<tr><td style="width:40%"><b>${k}</b></td><td>${v||"—"}</td></tr>`).join("");

  // بيانات المستأجر
  const tenantRows = [
    ["الاسم كامل", c.tenant_name],
    ["رقم الهوية", c.tenant_identity || "—"],
    ["رقم الجوال", c.tenant_phone || "—"],
    ["البريد", c.tenant_email || "—"],
  ];
  info_tenant.innerHTML = tenantRows.map(([k,v])=>`<tr><td style="width:40%"><b>${k}</b></td><td>${v||"—"}</td></tr>`).join("");

  // البيانات المالية
  const installmentsCount = sch.length || 0;
  const each = installmentsCount ? (Number(c.total_amount||0)/installmentsCount) : 0;
  const finRows = [
    ["مبلغ الإيجار (إجمالي)", fmt(c.total_amount) + " ريال"],
    ["نوع السداد", planLabel(c.payment_plan)],
    ["قيمة كل دفعة (تقريبي)", installmentsCount? (each.toFixed(2)+" ريال") : "—"],
    ["إيجار الوحدة (إن وجد)", fmt(c.rent_amount) + " ريال"],
  ];
  info_fin.innerHTML = finRows.map(([k,v])=>`<tr><td style="width:40%"><b>${k}</b></td><td>${v||"—"}</td></tr>`).join("");

  // المدفوعات
  payRows.innerHTML = "";
  if(!pays.length){
    payRows.innerHTML = `<tr><td colspan="4" class="empty">لا توجد مدفوعات</td></tr>`;
  } else {
    for(const p of pays){
      const receipt = `<a href="../receipt.php?paymentId=${p.id}" target="_blank">فتح</a>`;
      payRows.innerHTML += `<tr><td>${p.paid_at||""}</td><td>${fmt(p.amount)}</td><td>${p.source||""}</td><td>${receipt}</td></tr>`;
    }
  }

  // جدول السداد
  const today = new Date();
  schRows.innerHTML = "";
  if(!sch.length){
    schRows.innerHTML = `<tr><td colspan="7" class="empty">لا يوجد جدول سداد</td></tr>`;
  } else {
    for(const it of sch){
      const due = new Date((it.due_date||"")+"T00:00:00");
      const diffDays = Math.floor((due - new Date(today.getFullYear(), today.getMonth(), today.getDate())) / 86400000);
      const remainCell = it.status==='paid' ? '<span class="s-badge ok">0 يوم</span>' : schDaysBadge(diffDays);
      const act = (it.status==='unpaid')
        ? `<button class="btn mini primary" onclick="paySchedule(${it.id}, ${Number(it.amount||0)})">سداد</button>`
        : `<span class="small">—</span>`;
      schRows.innerHTML += `<tr>
        <td>${it.seq}</td>
        <td>${it.due_date}</td>
        <td>${fmt(it.amount)}</td>
        <td>${badgeStatus(it.status)}</td>
        <td>${it.paid_at||""}</td>
        <td>${remainCell}</td>
        <td style="white-space:nowrap">${act}</td>
      </tr>`;
    }
  }
}

function planLabel(p){
  if(p==='one') return 'دفعة واحدة';
  if(p==='two') return 'دفعتين';
  if(p==='three') return 'ثلاث دفعات';
  if(p==='four') return 'أربع دفعات';
  if(p==='monthly') return 'شهري';
  return p||'—';
}

function exportExcel(){ window.location.href="../../api/admin/contract_export_excel.php?id="+id; }
function exportPDF(){ window.location.href="../../api/admin/contract_export_pdf.php?id="+id; }

load();
</script>

<?php require __DIR__.'/_layout/footer.php'; ?>
