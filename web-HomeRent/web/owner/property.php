<?php
declare(strict_types=1);
$active='properties';
$title='إدارة العقار';
require __DIR__.'/_layout/boot.php';
require __DIR__.'/_layout/header.php';
require __DIR__.'/_layout/sidebar.php';
?>

<div class="section-title">
  <div>
    <h2 id="pTitle">إدارة العقار</h2>
    <div class="small" id="pSub">—</div>
  </div>
  <div class="row" style="justify-content:flex-start; gap:10px">
    <button class="btn" onclick="history.back()">رجوع</button>
    <button class="btn primary" onclick="load()">تحديث</button>
  </div>
</div>

<div class="grid cols2">
  <div class="card">
    <h3 style="margin:0 0 8px">بيانات العقار</h3>

    <div class="grid cols2" style="gap:10px">
      <div>
        <label>اسم العقار</label>
        <input class="input" id="p_name" placeholder="اسم العقار"/>
      </div>
      <div>
        <label>المدينة</label>
        <input class="input" id="p_city" placeholder="المدينة (اختياري)"/>
      </div>
      <div>
        <label>الحي</label>
        <input class="input" id="p_dist" placeholder="الحي (اختياري)"/>
      </div>
      <div style="display:flex;align-items:end;gap:10px">
        <button class="btn primary" onclick="saveProperty()">حفظ التعديل</button>
        <button class="btn danger" onclick="deleteProperty()">حذف العقار</button>
      </div>
    </div>

    <div class="notice warn" style="margin-top:10px">
      <b>ملاحظة:</b> لا يمكن حذف العقار إذا كان لديه عقود مرتبطة.
    </div>

    <div id="pMsg" class="small" style="margin-top:10px"></div>
  </div>

  <div class="card">
    <h3 style="margin:0 0 8px">إضافة وحدة</h3>
    <div class="grid cols2" style="gap:10px">
      <div>
        <label>نوع الوحدة</label>
        <input class="input" id="u_type" placeholder="شقة / معرض / ..."/>
      </div>
      <div>
        <label>اسم/رقم الوحدة</label>
        <input class="input" id="u_name" placeholder="مثال: 11"/>
      </div>
      <div>
        <label>قيمة الإيجار</label>
        <input class="input" id="u_rent" placeholder="اختياري"/>
      </div>
      <div style="display:flex;align-items:end">
        <button class="btn primary" onclick="createUnit()">إضافة</button>
      </div>
    </div>
    <div id="uMsg" class="small" style="margin-top:10px"></div>
  </div>
</div>

<div class="card" style="margin-top:12px">
  <div class="section-title">
    <div>
      <h3 style="margin:0">الوحدات</h3>
      <div class="small">تعديل/حذف وحدات العقار.</div>
    </div>
  </div>

  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>#</th><th>النوع</th><th>الاسم/الرقم</th><th>الإيجار</th><th>الحالة</th><th>إجراء</th>
        </tr>
      </thead>
      <tbody id="units"></tbody>
    </table>
  </div>
</div>

<script>
const id = new URLSearchParams(location.search).get("id");
function fmt(n){ return (Number(n||0)).toFixed(2); }
function statusBadge(st){
  if(st==="rented") return '<span class="s-badge ok">مؤجرة</span>';
  return '<span class="s-badge gray">متاحة</span>';
}

async function load(){
  if(!id){ alert("معرف العقار غير موجود"); location.href="properties.php"; return; }

  const r = await api("/admin/property_get.php?id="+id);
  const p = r.item || r.property || {};
  const u = r.units || [];

  pTitle.textContent = "إدارة العقار: " + (p.name || ("#"+id));
  pSub.textContent = (p.city||"—") + " — " + (p.district||"—");

  p_name.value = p.name || "";
  p_city.value = p.city || "";
  p_dist.value = p.district || "";

  // units
  units.innerHTML = "";
  if(!u.length){
    units.innerHTML = `<tr><td colspan="6" class="empty">لا توجد وحدات</td></tr>`;
    return;
  }
  for(const it of u){
    const tr=document.createElement("tr");
    tr.innerHTML = `
      <td>${it.id||""}</td>
      <td>${it.unit_type||"-"}</td>
      <td>${it.name||"-"}</td>
      <td>${fmt(it.rent_amount||0)}</td>
      <td>${statusBadge(it.status)}</td>
      <td style="white-space:nowrap">
        <button class="btn mini" onclick="editUnit(${it.id}, '${(it.unit_type||'').replace(/'/g,'\'')}', '${(it.name||'').replace(/'/g,'\'')}', ${Number(it.rent_amount||0)}, '${it.status||'available'}')">تعديل</button>
        <button class="btn mini danger" onclick="deleteUnit(${it.id})">حذف</button>
      </td>
    `;
    units.appendChild(tr);
  }
}

async function saveProperty(){
  pMsg.textContent = "";
  try{
    await api("/admin/properties_update.php",{method:"POST", body:{propertyId:Number(id), name:p_name.value, city:p_city.value, district:p_dist.value}});
    pMsg.textContent = "✅ تم حفظ التعديل";
    await load();
  }catch(e){
    pMsg.textContent = "❌ " + e.message;
  }
}

async function deleteProperty(){
  if(!confirm("تأكيد حذف العقار؟ لن يمكنك استرجاعه.")) return;
  pMsg.textContent = "";
  try{
    await api("/admin/properties_delete.php",{method:"POST", body:{propertyId:Number(id)}});
    alert("تم حذف العقار");
    location.href = "properties.php";
  }catch(e){
    pMsg.textContent = "❌ " + e.message;
  }
}

async function createUnit(){
  uMsg.textContent="";
  try{
    const r=await api("/admin/units_create.php",{method:"POST", body:{propertyId:Number(id), unitType:u_type.value||"شقة", name:u_name.value, rentAmount:Number(u_rent.value||0)}});
    uMsg.textContent="✅ تم إضافة الوحدة رقم: "+r.unitId;
    u_name.value=""; u_rent.value="";
    await load();
  }catch(e){ uMsg.textContent="❌ "+e.message; }
}

async function editUnit(unitId, unitType, name, rent, status){
  const nt = prompt("نوع الوحدة:", unitType||"شقة"); if(nt===null) return;
  const nn = prompt("اسم/رقم الوحدة:", name||""); if(nn===null || !nn.trim()) return;
  const nr = prompt("قيمة الإيجار:", String(rent||0)); if(nr===null) return;
  const ns = confirm("هل الوحدة مؤجرة؟ (موافق = مؤجرة)") ? "rented" : "available";
  try{
    await api("/admin/units_update.php",{method:"POST", body:{unitId:Number(unitId), unitType:nt, name:nn, rentAmount:Number(nr||0), status:ns}});
    ownerToast("✅ تم تعديل الوحدة", "ok");
    await load();
  }catch(e){ ownerToast("❌ "+e.message,"err"); }
}

async function deleteUnit(unitId){
  if(!confirm("حذف الوحدة؟")) return;
  try{
    await api("/admin/units_delete.php",{method:"POST", body:{unitId:Number(unitId)}});
    ownerToast("✅ تم حذف الوحدة", "ok");
    await load();
  }catch(e){ ownerToast("❌ "+e.message,"err"); }
}

load();
</script>

<?php require __DIR__.'/_layout/footer.php'; ?>
