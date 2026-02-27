// app.js — ثابت على QNAP + يدعم LocalStorage + Cookie fallback
const API_BASE = location.origin + "/web-HomeRent/api";

// ✅ حفظ التوكن: LocalStorage + Cookie (لضمان عدم ضياعه)
function saveToken(t){
  localStorage.setItem("ijarweb_token", t);

  // Cookie fallback (SameSite=Lax مناسب)
  document.cookie =
    "ijarweb_token=" + encodeURIComponent(t) +
    "; path=/web-HomeRent/; SameSite=Lax";
}

function getToken(){
  const t = localStorage.getItem("ijarweb_token");
  if (t) return t;

  // fallback من cookie
  const m = document.cookie.match(/(?:^|;\s*)ijarweb_token=([^;]+)/);
  return m ? decodeURIComponent(m[1]) : "";
}

// ✅ تسجيل خروج ثابت من أي صفحة
function logout(){
  localStorage.removeItem("ijarweb_token");
  document.cookie = "ijarweb_token=; path=/web-HomeRent/; expires=Thu, 01 Jan 1970 00:00:00 GMT";
  location.href = location.origin + "/web-HomeRent/web/login.html";
}

// ✅ API Helper: يرجع JSON مضبوط أو يعطيك نص الخطأ أول 250 حرف
async function api(path,{method="GET",body=null,isForm=false}={}){
  const headers = {};
  const token = getToken();
  if (token) headers["Authorization"] = "Bearer " + token;

  if (body && !isForm) headers["Content-Type"] = "application/json";

  const res = await fetch(API_BASE + path, {
    method,
    headers,
    body: body && !isForm ? JSON.stringify(body) : body
  });

  const text = await res.text();

  let data;
  try {
    data = JSON.parse(text);
  } catch (e) {
    throw new Error("Bad JSON: " + text.slice(0, 250));
  }

  // ✅ إذا Unauthorized رجّع المستخدم للّوجن مباشرة
  if (res.status === 401 || data.message === "Unauthorized") {
    throw new Error("Unauthorized");
  }

  if (!data.ok) throw new Error(data.message || "API Error");

  return data;
}