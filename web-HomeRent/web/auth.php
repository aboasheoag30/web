<?php
// auth.php - صفحة واحدة (دخول + تسجيل مالك) بتصميم رسمي (كرت واحد بالمنتصف)
declare(strict_types=1);

$tab = (string)($_GET['tab'] ?? 'login');
if (!in_array($tab, ['login','register'], true)) $tab = 'login';
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title><?= $tab==='register' ? 'إنشاء حساب - ايجار ويب' : 'تسجيل الدخول - ايجار ويب' ?></title>

  <!-- لو عندك official.css ويهمك توحيد الشكل مع النظام، اتركه -->
  <link rel="stylesheet" href="assets/official.css"/>
  <link rel="stylesheet" href="assets/auth.css"/>
</head>
<body>

<div class="auth-wrap">
  <div class="auth-card">

    <div class="auth-head">
      <div class="brand">
        <div class="logo">EW</div>
        <div>
          <div class="brand-title">ايجار ويب</div>
          <div class="brand-sub">منصة إدارة الإيجارات</div>
        </div>
      </div>
      <div style="color:#64748b;font-size:13px">
        سجّل دخولك لإدارة العقارات والعقود والمدفوعات، أو أنشئ حساب مالك جديد.
      </div>
    </div>

    <div class="tabs">
      <button class="tab <?= $tab==='login'?'active':'' ?>" id="tab_login" onclick="switchTab('login')" type="button">تسجيل الدخول</button>
      <button class="tab <?= $tab==='register'?'active':'' ?>" id="tab_register" onclick="switchTab('register')" type="button">تسجيل</button>
    </div>

    <div class="auth-body">

      <!-- LOGIN -->
      <div id="pane_login" style="<?= $tab==='login'?'':'display:none' ?>">
        <div class="field">
          <div class="label">البريد الإلكتروني</div>
          <input id="login_email" class="input" inputmode="email" placeholder="name@example.com" autocomplete="username"/>
        </div>

        <div class="field">
          <div class="label">كلمة المرور</div>
          <input id="login_pw" type="password" class="input" placeholder="••••••••" autocomplete="current-password"/>
        </div>

        <div class="actions">
          <a class="link" href="./common/change_password.html">نسيت كلمة المرور؟</a>
        </div>

        <button class="btn primary" onclick="doLogin()" type="button">دخول</button>
        <div id="login_msg" class="msg"></div>
      </div>

      <!-- REGISTER OWNER -->
      <div id="pane_register" style="<?= $tab==='register'?'':'display:none' ?>">
        <div class="field">
          <div class="label">الاسم الكامل</div>
          <input id="reg_name" class="input" placeholder="الاسم الكامل" autocomplete="name"/>
        </div>

        <div class="row">
          <div class="field">
            <div class="label">البريد الإلكتروني</div>
            <input id="reg_email" class="input" inputmode="email" placeholder="name@example.com" autocomplete="email"/>
          </div>
          <div class="field">
            <div class="label">الجوال (اختياري)</div>
            <input id="reg_phone" class="input" inputmode="tel" placeholder="05xxxxxxxx" autocomplete="tel"/>
          </div>
        </div>

        <div class="row">
          <div class="field">
            <div class="label">كلمة المرور</div>
            <input id="reg_pw" type="password" class="input" placeholder="6 أحرف على الأقل" autocomplete="new-password"/>
          </div>
          <div class="field">
            <div class="label">تأكيد كلمة المرور</div>
            <input id="reg_pw2" type="password" class="input" placeholder="أعد إدخال كلمة المرور" autocomplete="new-password"/>
          </div>
        </div>

        <button class="btn primary" onclick="doRegisterOwner()" type="button">إنشاء حساب</button>

        <div id="reg_msg" class="msg"></div>

        <div class="actions" style="margin-top:10px">
          <div style="color:#64748b;font-size:12px">
            التسجيل هنا مخصص لحساب <b>مالك</b>. الموظفين يتم إضافتهم من لوحة المالك.
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<div class="foot">© <?= date('Y') ?> ايجار ويب</div>

<script src="assets/app.js"></script>
<script>
function setMsg(el, text, kind){
  el.classList.remove('show','err','ok');
  el.textContent = text || '';
  if(!text) return;
  el.classList.add('show');
  if(kind==='err') el.classList.add('err');
  if(kind==='ok')  el.classList.add('ok');
}

function switchTab(t){
  const isLogin = (t === 'login');
  document.getElementById('pane_login').style.display = isLogin ? '' : 'none';
  document.getElementById('pane_register').style.display = isLogin ? 'none' : '';
  document.getElementById('tab_login').classList.toggle('active', isLogin);
  document.getElementById('tab_register').classList.toggle('active', !isLogin);

  const url = new URL(location.href);
  url.searchParams.set('tab', t);
  history.replaceState({}, '', url.toString());
}

async function doLogin(){
  const msg = document.getElementById('login_msg');
  setMsg(msg, '', '');

  const email = (login_email.value||'').trim();
  const password = (login_pw.value||'');

  if(!email || !password){
    setMsg(msg, '❌ أدخل البريد وكلمة المرور', 'err');
    return;
  }

  try{
    // حسب مشروعك الحالي
    const data = await api('/auth/login.php', {method:'POST', body:{email, password}});
    if(data?.token) saveToken(data.token);

    const role = data?.user?.role || '';

    if(role === 'tenant') location.href = './tenant/panel.html';
    else if(role === 'site_admin' || role === 'site_admin_staff') location.href = './site-admin/panel.html';
    else if(role === 'owner' || role === 'staff') {
      if (await exists('owner/dashboard.php')) location.href = './owner/dashboard.php';
      else location.href = './owner/panel.html';
    }
    else throw new Error('نوع حساب غير معروف');

  }catch(e){
    setMsg(msg, '❌ ' + (e.message || 'فشل تسجيل الدخول'), 'err');
  }
}

async function doRegisterOwner(){
  const msg = document.getElementById('reg_msg');
  setMsg(msg, '', '');

  const fullName = (reg_name.value||'').trim();
  const email    = (reg_email.value||'').trim();
  const phone    = (reg_phone.value||'').trim();
  const password = (reg_pw.value||'');
  const password2= (reg_pw2.value||'');

  if(!fullName){ setMsg(msg, '❌ اكتب الاسم الكامل', 'err'); return; }
  if(!email){ setMsg(msg, '❌ اكتب البريد الإلكتروني', 'err'); return; }
  if(password.length < 6){ setMsg(msg, '❌ كلمة المرور يجب أن تكون 6 أحرف على الأقل', 'err'); return; }
  if(password !== password2){ setMsg(msg, '❌ كلمة المرور وتأكيدها غير متطابقين', 'err'); return; }

  try{
    // حسب مشروعك الحالي
    const data = await api('/auth/register_owner.php', {method:'POST', body:{fullName, email, phone, password}});
    if(data?.token) saveToken(data.token);

    setMsg(msg, '✅ تم إنشاء الحساب بنجاح، جاري التحويل...', 'ok');

    setTimeout(async ()=>{
      if (await exists('owner/dashboard.php')) location.href = './owner/dashboard.php';
      else location.href = './owner/panel.html';
    }, 600);

  }catch(e){
    setMsg(msg, '❌ ' + (e.message || 'فشل إنشاء الحساب'), 'err');
  }
}

// فحص ملف موجود (بدون API)
async function exists(path){
  try{
    const res = await fetch(path, {method:'HEAD', cache:'no-store'});
    return res.ok;
  }catch(_){ return false; }
}

// Enter = تنفيذ
document.addEventListener('keydown', (ev)=>{
  if(ev.key !== 'Enter') return;
  const loginVisible = document.getElementById('pane_login').style.display !== 'none';
  loginVisible ? doLogin() : doRegisterOwner();
});
</script>

</body>
</html>
