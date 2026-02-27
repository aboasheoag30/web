/* HomeRent Web - UI helpers (no frameworks) */

function hrGetToken() {
  return localStorage.getItem('homerent_token') || sessionStorage.getItem('homerent_token');
}

function hrGetUser() {
  const s = localStorage.getItem('homerent_user') || sessionStorage.getItem('homerent_user');
  try { return s ? JSON.parse(s) : null; } catch { return null; }
}

function hrLogout(redirectTo = '../../index.html') {
  localStorage.removeItem('homerent_token');
  localStorage.removeItem('homerent_user');
  sessionStorage.removeItem('homerent_token');
  sessionStorage.removeItem('homerent_user');
  window.location.replace(redirectTo);
}

function hrRequireAuth({ role = null, redirectTo = '../../index.html' } = {}) {
  const token = hrGetToken();
  const user = hrGetUser();
  if (!token || !user) {
    window.location.replace(redirectTo);
    return { ok: false, user: null };
  }
  if (role && user.role !== role) {
    hrToast('ليس لديك صلاحية لدخول هذه الصفحة.', 'error');
    setTimeout(() => hrLogout(redirectTo), 600);
    return { ok: false, user: null };
  }
  return { ok: true, user };
}

// ---------------- Toast ----------------
function hrEnsureToastRoot() {
  let root = document.getElementById('hr-toast-root');
  if (!root) {
    root = document.createElement('div');
    root.id = 'hr-toast-root';
    root.className = 'fixed z-[9999] top-4 right-4 left-4 md:left-auto md:w-[420px] space-y-2';
    document.body.appendChild(root);
  }
  return root;
}

function hrToast(message, type = 'info', timeout = 3200) {
  const root = hrEnsureToastRoot();

  const colors = {
    info:   'border-slate-200 bg-white text-slate-800',
    success:'border-emerald-200 bg-emerald-50 text-emerald-900',
    warning:'border-amber-200 bg-amber-50 text-amber-900',
    error:  'border-rose-200 bg-rose-50 text-rose-900'
  };

  const el = document.createElement('div');
  el.className = `hr-shadow flex items-start gap-3 rounded-2xl border p-4 ${colors[type] || colors.info}`;
  el.innerHTML = `
    <div class="mt-0.5 h-2.5 w-2.5 rounded-full ${type === 'success' ? 'bg-emerald-500' : type === 'warning' ? 'bg-amber-500' : type === 'error' ? 'bg-rose-500' : 'bg-slate-500'}"></div>
    <div class="flex-1 text-sm leading-6">${escapeHtml(message)}</div>
    <button class="-mt-1 rounded-xl px-2 py-1 text-slate-500 hover:bg-slate-100" aria-label="إغلاق">✕</button>
  `;

  const btn = el.querySelector('button');
  btn.addEventListener('click', () => el.remove());
  root.appendChild(el);

  if (timeout > 0) {
    setTimeout(() => el.remove(), timeout);
  }
}

function escapeHtml(str) {
  return String(str)
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;');
}

// ---------------- Sidebar ----------------
function hrInitSidebar() {
  const openBtn = document.querySelector('[data-hr-sidebar-open]');
  const closeBtn = document.querySelector('[data-hr-sidebar-close]');
  const overlay = document.querySelector('[data-hr-sidebar-overlay]');
  const panel = document.querySelector('[data-hr-sidebar]');
  if (!panel) return;

  const open = () => {
    panel.classList.remove('translate-x-full');
    overlay?.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
  };
  const close = () => {
    panel.classList.add('translate-x-full');
    overlay?.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
  };

  openBtn?.addEventListener('click', open);
  closeBtn?.addEventListener('click', close);
  overlay?.addEventListener('click', close);
}

document.addEventListener('DOMContentLoaded', () => {
  hrInitSidebar();
});
