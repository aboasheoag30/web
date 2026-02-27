<?php
declare(strict_types=1);
$active='properties';
$title='العقارات والوحدات';
require __DIR__.'/_layout/boot.php';
require __DIR__.'/_layout/header.php';
require __DIR__.'/_layout/sidebar.php';
?>

<div class="section-title">
  <div>
    <h2>العقارات والوحدات</h2>
    <div class="small">إضافة عقار، إضافة وحدة، وعرض قائمة العقارات</div>
  </div>
  <div class="row">
    <button class="btn primary" onclick="loadProperties()">تحديث القائمة</button>
  </div>
</div>

<div class="grid cols2">
  <div class="card">
    <h3 style="margin:0 0 6px">إضافة عقار</h3>
    <div class="small">أدخل البيانات الأساسية ثم احفظ.</div>

    <div class="grid" style="margin-top:10px">
      <div>
        <label>اسم العقار</label>
        <input id="p_name" class="input" placeholder="اسم العقار"/>
      </div>
      <div class="row">
        <div>
          <label>المدينة (اختياري)</label>
          <input id="p_city" class="input" placeholder="المدينة"/>
        </div>
        <div>
          <label>الحي (اختياري)</label>
          <input id="p_dist" class="input" placeholder="الحي"/>
        </div>
      </div>

      <div class="row">
        <button class="btn primary" onclick="createProperty()">حفظ</button>
        <button class="btn" onclick="resetPropertyForm()">مسح</button>
      </div>

      <div id="p_msg" class="small"></div>
    </div>

    <hr>

    <div class="section-title">
      <div>
        <h3>قائمة العقارات</h3>
        <div class="small">اضغط “فتح” لعرض تفاصيل العقار.</div>
      </div>
    </div>

    <div id="props"></div>
  </div>

  <div class="card">
    <h3 style="margin:0 0 6px">إضافة وحدة لعقار</h3>
    <div class="small">اختر العقار ثم أضف الوحدة.</div>

    <div class="grid" style="margin-top:10px">
      <div>
        <label>العقار</label>
        <select id="u_prop" class="input"></select>
      </div>

      <div class="row">
        <div>
          <label>نوع الوحدة</label>
          <input id="u_type" class="input" placeholder="شقة/فيلا/معرض..." />
        </div>
        <div>
          <label>اسم/رقم الوحدة</label>
          <input id="u_name" class="input" placeholder="مثل 11" />
        </div>
      </div>

      <div>
        <label>قيمة الإيجار (اختياري)</label>
        <input id="u_rent" class="input" placeholder="0" />
      </div>

      <div class="row">
        <button class="btn primary" onclick="createUnit()">حفظ الوحدة</button>
        <button class="btn" onclick="resetUnitForm()">مسح</button>
      </div>

      <div id="u_msg" class="small"></div>

      <div class="card" style="background:#fbfbfc">
        <div class="small">يمكنك لاحقًا إدارة الوحدات وعقودها من صفحة تفاصيل العقار.</div>
      </div>
    </div>
  </div>
</div>

<script>
function esc(s){
  return String(s ?? '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]));
}
function pill(text){ return `<span class="pill">${esc(text)}</span>`; }

function resetPropertyForm(){ p_name.value=""; p_city.value=""; p_dist.value=""; p_msg.textContent=""; }
function resetUnitForm(){ u_type.value=""; u_name.value=""; u_rent.value=""; u_msg.textContent=""; }

async function loadProperties(){
  const p = await api("/admin/properties_list.php");
  const list = document.getElementById("props");
  const sel  = document.getElementById("u_prop");
  list.innerHTML = "";
  sel.innerHTML = "";

  const items = p.items || [];
  if(!items.length){
    list.innerHTML = `<div class="empty">لا توجد عقارات</div>`;
    sel.disabled = true;
    const opt=document.createElement("option"); opt.value=""; opt.textContent="لا توجد عقارات"; sel.appendChild(opt);
    return;
  }
  sel.disabled = false;

  for(const it of items){
    const cityDist = [it.city||"", it.district||""].join(" ").trim();
    const div=document.createElement("div");
    div.style.padding="10px 0";
    div.style.borderBottom="1px dashed rgba(15,23,42,.18)";
    div.innerHTML = `
      ${pill("#"+it.id)} <b>${esc(it.name)}</b>
      <div class="small" style="margin-top:6px">${esc(cityDist || "—")}</div>
      <div class="actions" style="margin-top:10px">
        <button class="btn" onclick="location.href='property.php?id=${Number(it.id)}'">إدارة</button>
        <button class="btn danger" onclick="deleteProperty(${Number(it.id)}, '${'${esc(it.name)}'}')">حذف</button>
      </div>
    `;
    list.appendChild(div);

    const opt=document.createElement("option");
    opt.value=it.id;
    opt.textContent = `${it.name} (#${it.id})`;
    sel.appendChild(opt);
  }
  ownerToast("تم تحديث القائمة", "ok");
}


async function deleteProperty(pid, name){
  if(!confirm("تأكيد حذف العقار: " + (name||("#"+pid)) + " ؟")) return;
  try{
    await api("/admin/properties_delete.php",{method:"POST",body:{propertyId:Number(pid)}});
    ownerToast("✅ تم حذف العقار", "ok");
    await loadProperties();
  }catch(e){
    ownerToast("❌ "+e.message, "err");
  }
}

async function createProperty(){
  p_msg.textContent="";
  const name=(p_name.value||"").trim();
  if(!name){ p_msg.textContent="❌ اكتب اسم العقار"; return; }
  try{
    const r = await api("/admin/properties_create.php",{method:"POST",body:{name,city:(p_city.value||"").trim(),district:(p_dist.value||"").trim()}});
    p_msg.textContent = "✅ تم حفظ العقار رقم: " + r.propertyId;
    resetPropertyForm();
    await loadProperties();
  }catch(e){
    p_msg.textContent="❌ "+(e.message||"تعذر الحفظ");
  }
}

async function createUnit(){
  u_msg.textContent="";
  const propertyId = Number(u_prop.value||0);
  if(!propertyId){ u_msg.textContent="❌ اختر العقار"; return; }

  const unitType = (u_type.value||"شقة").trim() || "شقة";
  const name = (u_name.value||"").trim();
  const rentAmount = Number(u_rent.value||0);
  if(!name){ u_msg.textContent="❌ اكتب اسم/رقم الوحدة"; return; }

  try{
    const r = await api("/admin/units_create.php",{method:"POST",body:{propertyId,unitType,name,rentAmount}});
    u_msg.textContent = "✅ تم حفظ الوحدة رقم: " + r.unitId;
    resetUnitForm();
    ownerToast("تمت إضافة الوحدة", "ok");
  }catch(e){
    u_msg.textContent="❌ "+(e.message||"تعذر الحفظ");
  }
}

async function boot(){
  try{
    const me = await api("/me.php");
    if(!(["owner","staff"]).includes(me.me.role)) { alert("ليس لديك صلاحية"); logout(); return; }
    await loadProperties();
  }catch(e){ alert(e.message||"تعذر التحميل"); logout(); }
}
boot();
</script>

<?php require __DIR__.'/_layout/footer.php'; ?>
