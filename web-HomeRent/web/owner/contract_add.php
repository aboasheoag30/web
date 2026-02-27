<?php
declare(strict_types=1);
$active='contracts';
$title='إضافة عقد';
require __DIR__.'/_layout/boot.php';
require __DIR__.'/_layout/header.php';
require __DIR__.'/_layout/sidebar.php';
?>

<div class="section-title">
  <div>
    <h2>إضافة عقد</h2>
    <div class="small">سيتم إنشاء جدول السداد تلقائيًا حسب بداية العقد ونوع السداد.</div>
  </div>
  <div class="row" style="justify-content:flex-start; gap:10px">
    <button class="btn" onclick="location.href='contracts.php'">رجوع</button>
  </div>
</div>

<div class="grid cols2">
  <div class="card">
    <h3 style="margin:0 0 10px">بيانات العقد</h3>

    <div class="grid cols2" style="gap:10px">
      <div>
        <label>رقم العقد</label>
        <input class="input" id="contractNumber" placeholder="مثال: 2026-001"/>
      </div>
      <div>
        <label>بداية العقد</label>
        <input class="input" id="startDate" type="date"/>
      </div>

      <div>
        <label>مدة العقد (بالأشهر)</label>
        <input class="input" id="months" type="number" min="1" value="12"/>
      </div>
      <div>
        <label>نوع السداد</label>
        <select class="input" id="paymentPlan">
          <option value="one">دفعة واحدة</option>
          <option value="two">دفعتين</option>
          <option value="three">ثلاث دفعات</option>
          <option value="four">أربع دفعات</option>
          <option value="monthly" selected>شهري</option>
        </select>
      </div>

      <div>
        <label>مبلغ الإيجار (إجمالي العقد)</label>
        <input class="input" id="totalAmount" type="number" min="1" placeholder="مثال: 60000"/>
      </div>
      <div>
        <label>قيمة كل دفعة (تقريبي)</label>
        <input class="input" id="eachAmount" disabled/>
      </div>
    </div>

    <hr class="sep"/>

    <h3 style="margin:0 0 10px">بيانات العقار والوحدة</h3>
    <div class="grid cols2" style="gap:10px">
      <div>
        <label>العقار</label>
        <select class="input" id="propertyId" onchange="loadUnits()"></select>
      </div>
      <div>
        <label>الوحدة</label>
        <select class="input" id="unitId"></select>
      </div>
    </div>

    <hr class="sep"/>

    <h3 style="margin:0 0 10px">بيانات المستأجر</h3>
    <div class="grid cols2" style="gap:10px">
      <div>
        <label>الاسم كامل</label>
        <input class="input" id="tenantName" placeholder="اسم المستأجر"/>
      </div>
      <div>
        <label>رقم الجوال</label>
        <input class="input" id="tenantPhone" placeholder="05xxxxxxxx"/>
      </div>
      <div>
        <label>البريد الإلكتروني</label>
        <input class="input" id="tenantEmail" placeholder="example@email.com"/>
      </div>
      <div>
        <label>رقم الهوية</label>
        <input class="input" id="tenantIdentity" placeholder="اختياري (يتطلب تفعيل العمود)"/>
      </div>
    </div>

    <div class="row" style="margin-top:12px; justify-content:flex-start; gap:10px">
      <button class="btn primary" onclick="save()">حفظ العقد</button>
      <button class="btn" onclick="previewSchedule()">معاينة جدول السداد</button>
    </div>

    <div id="msg" class="small" style="margin-top:10px"></div>
  </div>

  <div class="card">
    <div class="section-title">
      <div>
        <h3 style="margin:0">معاينة جدول السداد</h3>
        <div class="small" id="endInfo">—</div>
      </div>
    </div>

    <div class="table-wrap">
      <table>
        <thead><tr><th>#</th><th>موعد الاستحقاق</th><th>المبلغ</th></tr></thead>
        <tbody id="schPrev"></tbody>
      </table>
    </div>

    <div class="notice" style="margin-top:10px">
      يتم حساب نهاية العقد = بداية العقد + المدة - يوم واحد.
    </div>
  </div>
</div>

<script>
function pad(n){ return String(n).padStart(2,'0'); }
function ymd(d){ return d.getFullYear()+"-"+pad(d.getMonth()+1)+"-"+pad(d.getDate()); }
function addMonthsKeepDay(dt, months){
  const day = dt.getDate();
  const d = new Date(dt.getFullYear(), dt.getMonth(), 1);
  d.setMonth(d.getMonth() + months);
  const last = new Date(d.getFullYear(), d.getMonth()+1, 0).getDate();
  d.setDate(Math.min(day, last));
  return d;
}
function addDays(dt, days){ const d=new Date(dt); d.setDate(d.getDate()+days); return d; }

function offsets(plan, months){
  if(plan==='one') return [0];
  if(plan==='two') return [0,6];
  if(plan==='three') return [0,4,8];
  if(plan==='four') return [0,3,6,9];
  const arr=[]; for(let i=0;i<months;i++) arr.push(i); return arr;
}

async function loadProps(){
  const p = await api("/admin/properties_list.php");
  propertyId.innerHTML = "";
  const items = p.items || [];
  if(!items.length){ propertyId.innerHTML = "<option value=''>لا توجد عقارات</option>"; return; }
  for(const it of items){
    const opt=document.createElement("option");
    opt.value = it.id;
    opt.textContent = it.name + " (#"+it.id+")";
    propertyId.appendChild(opt);
  }
  await loadUnits();
}

async function loadUnits(){
  unitId.innerHTML = "";
  const pid = Number(propertyId.value||0);
  if(!pid){ unitId.innerHTML = "<option value=''>اختر عقار</option>"; return; }
  const r = await api("/admin/property_get.php?id="+pid);
  const u = r.units || [];
  if(!u.length){ unitId.innerHTML = "<option value=''>لا توجد وحدات</option>"; return; }
  for(const it of u){
    const opt=document.createElement("option");
    opt.value = it.id;
    opt.textContent = (it.unit_type||"") + " " + (it.name||"") + " — " + (Number(it.rent_amount||0).toFixed(2)) + " ريال";
    unitId.appendChild(opt);
  }
}

function previewSchedule(){
  schPrev.innerHTML = "";
  msg.textContent = "";
  const sd = startDate.value;
  const m = Number(months.value||0);
  const plan = paymentPlan.value;
  const tot = Number(totalAmount.value||0);
  if(!sd || !m || !tot){ schPrev.innerHTML = `<tr><td colspan="3" class="empty">أدخل بداية العقد + المدة + المبلغ</td></tr>`; return; }
  const start = new Date(sd+"T00:00:00");
  const end = addDays(addMonthsKeepDay(start, m), -1);
  endInfo.textContent = "نهاية العقد: " + ymd(end) + " — مدة: " + m + " شهر";
  const offs = offsets(plan, m);
  const n = offs.length;
  const base = Math.floor((tot/n)*100)/100;
  const last = Math.round((tot - base*(n-1))*100)/100;

  eachAmount.value = (tot/n).toFixed(2) + " ريال";

  for(let i=0;i<n;i++){
    const due = addMonthsKeepDay(start, offs[i]);
    const amt = (i===n-1)? last : base;
    const tr=document.createElement("tr");
    tr.innerHTML = `<td>${i+1}</td><td>${ymd(due)}</td><td>${amt.toFixed(2)}</td>`;
    schPrev.appendChild(tr);
  }
}

async function save(){
  msg.textContent = "";
  try{
    if(!contractNumber.value.trim()) throw new Error("رقم العقد مطلوب");
    if(!startDate.value) throw new Error("بداية العقد مطلوبة");
    if(!Number(months.value||0)) throw new Error("مدة العقد مطلوبة");
    if(!Number(totalAmount.value||0)) throw new Error("مبلغ الإيجار مطلوب");
    if(!tenantName.value.trim()) throw new Error("اسم المستأجر مطلوب");
    if(!tenantEmail.value.trim()) throw new Error("بريد المستأجر مطلوب");
    if(!Number(propertyId.value||0) || !Number(unitId.value||0)) throw new Error("اختر العقار والوحدة");

    const body = {
      tenantName: tenantName.value,
      tenantEmail: tenantEmail.value,
      tenantPhone: tenantPhone.value,
      tenantIdentity: tenantIdentity.value,
      propertyId: Number(propertyId.value),
      unitId: Number(unitId.value),
      contractNumber: contractNumber.value,
      startDate: startDate.value,
      months: Number(months.value),
      paymentPlan: paymentPlan.value,
      totalAmount: Number(totalAmount.value)
    };

    const r = await api("/admin/contracts_create.php", {method:"POST", body});
    msg.textContent = "✅ تم إنشاء العقد";
    // افتح صفحة العقد مباشرة
    location.href = "contract.php?id=" + r.contractId;
  }catch(e){
    msg.textContent = "❌ " + e.message;
  }
}

['startDate','months','paymentPlan','totalAmount'].forEach(id=>{
  document.getElementById(id).addEventListener('change', previewSchedule);
  document.getElementById(id).addEventListener('input', previewSchedule);
});

loadProps().then(previewSchedule);
</script>

<?php require __DIR__.'/_layout/footer.php'; ?>
