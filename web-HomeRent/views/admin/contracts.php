<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>العقود | إيجار ويب</title>

  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: { cairo: ['Cairo', 'ui-sans-serif', 'system-ui'] },
          colors: { hr: { bg: '#f6f7fb' } }
        }
      }
    }
  </script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../../assets/css/app.css" />
</head>

<body class="min-h-screen bg-hr-bg font-cairo text-slate-900">

  <div data-hr-sidebar-overlay class="fixed inset-0 z-40 hidden bg-slate-900/40"></div>

  <div class="flex min-h-screen">

    <!-- Sidebar (desktop) -->
    <aside class="hidden lg:flex w-72 flex-col border-l border-slate-200/80 bg-white">
      <div class="h-20 px-6 flex items-center gap-3 border-b border-slate-200/70">
        <div class="h-11 w-11 rounded-2xl bg-blue-600 text-white flex items-center justify-center hr-shadow">
          <i class="fa-solid fa-house-laptop"></i>
        </div>
        <div>
          <div class="font-extrabold">إيجار ويب</div>
          <div class="text-xs text-slate-500">لوحة الإدارة</div>
        </div>
      </div>

      <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
        <a href="dashboard.php" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-slate-600 hover:bg-slate-50 border border-transparent hover:border-slate-200">
          <i class="fa-solid fa-chart-pie w-5"></i>
          <span class="font-semibold">الرئيسية</span>
        </a>
        <a href="properties.php" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-slate-600 hover:bg-slate-50 border border-transparent hover:border-slate-200">
          <i class="fa-solid fa-building w-5"></i>
          <span class="font-semibold">العقارات</span>
        </a>
        <a href="contracts.php" class="flex items-center gap-3 rounded-2xl px-4 py-3 bg-blue-50 text-blue-700 border border-blue-100">
          <i class="fa-solid fa-file-signature w-5 text-blue-700"></i>
          <span class="font-bold">العقود</span>
        </a>
        <a href="finance.php" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-slate-600 hover:bg-slate-50 border border-transparent hover:border-slate-200">
          <i class="fa-solid fa-sack-dollar w-5"></i>
          <span class="font-semibold">المالية</span>
        </a>
        <a href="#" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-slate-600 hover:bg-slate-50 border border-transparent hover:border-slate-200">
          <i class="fa-solid fa-bell w-5"></i>
          <span class="font-semibold">التنبيهات</span>
        </a>
        <a href="#" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-slate-600 hover:bg-slate-50 border border-transparent hover:border-slate-200">
          <i class="fa-solid fa-gear w-5"></i>
          <span class="font-semibold">الإعدادات</span>
        </a>
      </nav>

      <div class="p-4 border-t border-slate-200/70">
        <button onclick="hrLogout('../../index.html')" class="w-full rounded-2xl px-4 py-3 text-rose-700 bg-rose-50 border border-rose-100 hover:bg-rose-100 font-bold flex items-center justify-center gap-2">
          <i class="fa-solid fa-right-from-bracket"></i>
          <span>تسجيل الخروج</span>
        </button>
      </div>
    </aside>

    <!-- Sidebar (mobile drawer) -->
    <aside data-hr-sidebar class="lg:hidden fixed top-0 right-0 z-50 h-full w-80 max-w-[86vw] bg-white border-l border-slate-200/80 transform translate-x-full transition-transform duration-200">
      <div class="h-20 px-5 flex items-center justify-between border-b border-slate-200/70">
        <div class="flex items-center gap-3">
          <div class="h-11 w-11 rounded-2xl bg-blue-600 text-white flex items-center justify-center hr-shadow">
            <i class="fa-solid fa-house-laptop"></i>
          </div>
          <div>
            <div class="font-extrabold">إيجار ويب</div>
            <div class="text-xs text-slate-500">لوحة الإدارة</div>
          </div>
        </div>
        <button data-hr-sidebar-close class="h-10 w-10 rounded-2xl hover:bg-slate-100 text-slate-600" aria-label="إغلاق القائمة">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>
      <nav class="p-4 space-y-2 overflow-y-auto">
        <a href="dashboard.php" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-slate-600 hover:bg-slate-50 border border-transparent hover:border-slate-200">
          <i class="fa-solid fa-chart-pie w-5"></i>
          <span class="font-semibold">الرئيسية</span>
        </a>
        <a href="properties.php" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-slate-600 hover:bg-slate-50 border border-transparent hover:border-slate-200">
          <i class="fa-solid fa-building w-5"></i>
          <span class="font-semibold">العقارات</span>
        </a>
        <a href="contracts.php" class="flex items-center gap-3 rounded-2xl px-4 py-3 bg-blue-50 text-blue-700 border border-blue-100">
          <i class="fa-solid fa-file-signature w-5 text-blue-700"></i>
          <span class="font-bold">العقود</span>
        </a>
        <a href="finance.php" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-slate-600 hover:bg-slate-50 border border-transparent hover:border-slate-200">
          <i class="fa-solid fa-sack-dollar w-5"></i>
          <span class="font-semibold">المالية</span>
        </a>
      </nav>
      <div class="p-4 border-t border-slate-200/70">
        <button onclick="hrLogout('../../index.html')" class="w-full rounded-2xl px-4 py-3 text-rose-700 bg-rose-50 border border-rose-100 hover:bg-rose-100 font-bold flex items-center justify-center gap-2">
          <i class="fa-solid fa-right-from-bracket"></i>
          <span>تسجيل الخروج</span>
        </button>
      </div>
    </aside>

    <!-- Main -->
    <div class="flex-1 flex flex-col min-w-0">

      <!-- Topbar -->
      <header class="sticky top-0 z-30 bg-white/75 hr-glass border-b border-slate-200/70">
        <div class="h-20 px-4 sm:px-6 lg:px-10 flex items-center gap-3">
          <button data-hr-sidebar-open class="lg:hidden h-11 w-11 rounded-2xl hover:bg-slate-100 text-slate-700" aria-label="فتح القائمة">
            <i class="fa-solid fa-bars"></i>
          </button>

          <div class="flex-1">
            <div class="relative">
              <i class="fa-solid fa-magnifying-glass absolute right-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
              <input id="globalSearch" class="w-full rounded-2xl border border-slate-200 bg-white px-12 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="بحث: رقم عقد / اسم / جوال / هوية" />
            </div>
          </div>

          <div class="flex items-center gap-2">
            <button id="newContractBtn" class="hidden sm:inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-4 py-3 text-sm font-extrabold text-white hover:bg-blue-700">
              <i class="fa-solid fa-plus"></i>
              <span>إضافة عقد</span>
            </button>

            <div class="hidden sm:flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-3 py-2">
              <div class="text-left">
                <div id="userNameDisplay" class="text-sm font-extrabold text-slate-800">—</div>
                <div class="text-xs text-slate-500">مدير النظام</div>
              </div>
              <div class="h-10 w-10 rounded-2xl bg-blue-600 text-white flex items-center justify-center">
                <i class="fa-solid fa-user-shield"></i>
              </div>
            </div>
          </div>
        </div>
      </header>

      <!-- Content -->
      <main class="flex-1 px-4 sm:px-6 lg:px-10 py-8">

        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4 mb-6">
          <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900">العقود</h1>
            <p class="text-slate-500 mt-1">إدارة العقود بسرعة — مع حالات واضحة وفلاتر قوية.</p>
          </div>
          <div class="flex items-center gap-2">
            <button class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-700 hover:bg-slate-50">
              <i class="fa-solid fa-file-arrow-down ml-2"></i>
              تصدير
            </button>
            <button class="rounded-2xl bg-blue-600 px-4 py-3 text-sm font-extrabold text-white hover:bg-blue-700" id="newContractBtn2">
              <i class="fa-solid fa-plus ml-2"></i>
              إضافة
            </button>
          </div>
        </div>

        <!-- KPI row -->
        <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-5">
          <div class="bg-white border border-slate-100 rounded-3xl p-5 hr-shadow">
            <div class="flex items-center justify-between">
              <div>
                <div class="text-sm font-bold text-slate-500">عقود فعّالة</div>
                <div id="kpiActive" class="text-3xl font-extrabold mt-2">—</div>
              </div>
              <div class="h-12 w-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <i class="fa-solid fa-circle-check"></i>
              </div>
            </div>
          </div>
          <div class="bg-white border border-slate-100 rounded-3xl p-5 hr-shadow">
            <div class="flex items-center justify-between">
              <div>
                <div class="text-sm font-bold text-slate-500">قريب الانتهاء</div>
                <div id="kpiExpSoon" class="text-3xl font-extrabold mt-2">—</div>
              </div>
              <div class="h-12 w-12 rounded-2xl bg-amber-50 text-amber-700 flex items-center justify-center">
                <i class="fa-solid fa-clock"></i>
              </div>
            </div>
          </div>
          <div class="bg-white border border-slate-100 rounded-3xl p-5 hr-shadow">
            <div class="flex items-center justify-between">
              <div>
                <div class="text-sm font-bold text-slate-500">منتهية</div>
                <div id="kpiExpired" class="text-3xl font-extrabold mt-2">—</div>
              </div>
              <div class="h-12 w-12 rounded-2xl bg-rose-50 text-rose-700 flex items-center justify-center">
                <i class="fa-solid fa-triangle-exclamation"></i>
              </div>
            </div>
          </div>
          <div class="bg-white border border-slate-100 rounded-3xl p-5 hr-shadow">
            <div class="flex items-center justify-between">
              <div>
                <div class="text-sm font-bold text-slate-500">متأخرات</div>
                <div id="kpiOverdue" class="text-3xl font-extrabold mt-2">—</div>
              </div>
              <div class="h-12 w-12 rounded-2xl bg-slate-100 text-slate-700 flex items-center justify-center">
                <i class="fa-solid fa-sack-dollar"></i>
              </div>
            </div>
          </div>
        </section>

        <!-- Filters -->
        <section class="bg-white border border-slate-100 rounded-3xl p-5 hr-shadow mb-5">
          <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
            <div class="md:col-span-2">
              <label class="text-xs font-bold text-slate-500">بحث</label>
              <input id="q" class="mt-1 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm" placeholder="رقم العقد / الاسم / جوال / هوية" />
            </div>
            <div>
              <label class="text-xs font-bold text-slate-500">الحالة</label>
              <select id="status" class="mt-1 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm bg-white">
                <option value="all">الكل</option>
                <option value="active">فعّال</option>
                <option value="expSoon">قريب الانتهاء</option>
                <option value="expired">منتهي</option>
              </select>
            </div>
            <div>
              <label class="text-xs font-bold text-slate-500">من</label>
              <input id="from" type="date" class="mt-1 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm" />
            </div>
            <div>
              <label class="text-xs font-bold text-slate-500">إلى</label>
              <input id="to" type="date" class="mt-1 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm" />
            </div>
          </div>
          <div class="flex flex-wrap items-center gap-2 mt-4">
            <button id="applyFilters" class="rounded-2xl bg-slate-900 px-4 py-2.5 text-sm font-extrabold text-white hover:bg-slate-800">
              تطبيق
            </button>
            <button id="resetFilters" class="rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50">
              مسح
            </button>
            <div class="text-xs text-slate-500">* صفحة UI جاهزة للربط مع API.</div>
          </div>
        </section>

        <!-- Table -->
        <section class="bg-white border border-slate-100 rounded-3xl hr-shadow overflow-hidden">
          <div class="p-5 flex items-center justify-between">
            <div>
              <div class="text-lg font-extrabold">قائمة العقود</div>
              <div class="text-sm text-slate-500">سريع، واضح، مع أكشنات أساسية.</div>
            </div>
            <div class="text-sm text-slate-500">آخر تحديث: <span id="lastUpdate">—</span></div>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
              <thead class="bg-slate-50 text-slate-600">
                <tr>
                  <th class="text-right px-6 py-4 font-extrabold">رقم العقد</th>
                  <th class="text-right px-6 py-4 font-extrabold">العقار / الوحدة</th>
                  <th class="text-right px-6 py-4 font-extrabold">المستأجر</th>
                  <th class="text-right px-6 py-4 font-extrabold">الفترة</th>
                  <th class="text-right px-6 py-4 font-extrabold">المتبقي</th>
                  <th class="text-right px-6 py-4 font-extrabold">المتأخرات</th>
                  <th class="text-right px-6 py-4 font-extrabold">إجراءات</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100" id="rows"></tbody>
            </table>
          </div>
        </section>

        <div class="mt-10 text-center text-xs text-slate-500">© <span id="year"></span> إيجار ويب</div>
      </main>
    </div>
  </div>

  <script src="../../assets/js/app.js"></script>
  <script>
    const auth = hrRequireAuth({ redirectTo: '../../index.html' });
    if (auth.ok) document.getElementById('userNameDisplay').textContent = auth.user.name || 'مدير النظام';

    document.getElementById('year').textContent = new Date().getFullYear();
    document.getElementById('lastUpdate').textContent = new Date().toLocaleString('ar-SA');

    function badge(text, tone) {
      const map = {
        green: 'bg-emerald-50 text-emerald-700 border-emerald-200',
        red: 'bg-rose-50 text-rose-700 border-rose-200',
        amber: 'bg-amber-50 text-amber-800 border-amber-200',
        blue: 'bg-blue-50 text-blue-700 border-blue-200',
        slate: 'bg-slate-100 text-slate-700 border-slate-200',
      };
      return `<span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-extrabold ${map[tone] || map.blue}">${text}</span>`;
    }
    function daysBetween(a, b) {
      const ms = 24*60*60*1000;
      return Math.round((b.getTime() - a.getTime())/ms);
    }
    function fmtDate(d) {
      return new Date(d).toLocaleDateString('ar-SA');
    }
    function fmtMoney(n) {
      const v = Number(n || 0);
      return v.toLocaleString('ar-SA') + ' ر.س';
    }

    // Demo data
    const demo = [
      { no: '1023', prop: 'عمارة الرياض', unit: 'شقة 3', tenant: 'محمد أحمد', phone: '05xxxxxxxx', start: '2025-10-01', end: '2026-09-30', remainingDays: 216, overdue: 1500 },
      { no: '998', prop: 'مجمع الندى', unit: 'شقة 11', tenant: 'سارة خالد', phone: '05xxxxxxxx', start: '2025-03-01', end: '2026-02-28', remainingDays: 1, overdue: 0 },
      { no: '777', prop: 'فيلا الرمال', unit: 'فيلا 1', tenant: 'عبدالله سالم', phone: '05xxxxxxxx', start: '2024-01-01', end: '2025-12-31', remainingDays: -58, overdue: 3500 },
    ];

    function statusTone(remDays) {
      if (remDays <= 0) return { label: 'منتهي', tone: 'red' };
      if (remDays <= 30) return { label: 'قريب الانتهاء', tone: 'amber' };
      return { label: 'فعّال', tone: 'green' };
    }

    function render(list) {
      const tbody = document.getElementById('rows');
      tbody.innerHTML = list.map(c => {
        const s = statusTone(c.remainingDays);
        const remBadge = badge(`${Math.max(0,c.remainingDays)} يوم`, c.remainingDays <= 30 ? 'amber' : 'blue');
        const overdueBadge = c.overdue > 0 ? badge(fmtMoney(c.overdue), 'red') : badge('لا يوجد', 'green');
        return `
          <tr class="hover:bg-slate-50/60">
            <td class="px-6 py-4">
              <div class="font-extrabold text-slate-900">${c.no}</div>
              <div class="mt-1">${badge(s.label, s.tone)}</div>
            </td>
            <td class="px-6 py-4">
              <div class="font-extrabold">${c.prop}</div>
              <div class="text-sm text-slate-500">${c.unit}</div>
            </td>
            <td class="px-6 py-4">
              <div class="font-bold">${c.tenant}</div>
              <div class="text-sm text-slate-500" dir="ltr">${c.phone}</div>
            </td>
            <td class="px-6 py-4">
              <div class="font-bold">${fmtDate(c.start)} → ${fmtDate(c.end)}</div>
              <div class="text-sm text-slate-500">—</div>
            </td>
            <td class="px-6 py-4">${remBadge}</td>
            <td class="px-6 py-4">${overdueBadge}</td>
            <td class="px-6 py-4">
              <div class="flex flex-wrap gap-2">
                <button class="rounded-2xl border border-slate-200 bg-white px-3 py-2 text-xs font-extrabold text-slate-700 hover:bg-slate-50">تفاصيل</button>
                <button class="rounded-2xl bg-emerald-600 px-3 py-2 text-xs font-extrabold text-white hover:bg-emerald-700">سداد</button>
                <button class="rounded-2xl border border-slate-200 bg-white px-3 py-2 text-xs font-extrabold text-slate-700 hover:bg-slate-50">PDF</button>
              </div>
            </td>
          </tr>
        `;
      }).join('');

      const active = list.filter(x => x.remainingDays > 30).length;
      const expSoon = list.filter(x => x.remainingDays > 0 && x.remainingDays <= 30).length;
      const expired = list.filter(x => x.remainingDays <= 0).length;
      const overdueTotal = list.reduce((a,b) => a + (Number(b.overdue)||0), 0);
      document.getElementById('kpiActive').textContent = active.toLocaleString('ar-SA');
      document.getElementById('kpiExpSoon').textContent = expSoon.toLocaleString('ar-SA');
      document.getElementById('kpiExpired').textContent = expired.toLocaleString('ar-SA');
      document.getElementById('kpiOverdue').textContent = fmtMoney(overdueTotal);
    }

    function applyFilters() {
      const q = document.getElementById('q').value.trim();
      const status = document.getElementById('status').value;
      const from = document.getElementById('from').value;
      const to = document.getElementById('to').value;

      let list = [...demo];
      if (q) {
        const qq = q.toLowerCase();
        list = list.filter(x =>
          String(x.no).includes(qq) ||
          x.prop.toLowerCase().includes(qq) ||
          x.unit.toLowerCase().includes(qq) ||
          x.tenant.toLowerCase().includes(qq)
        );
      }
      if (status !== 'all') {
        list = list.filter(x => {
          const s = statusTone(x.remainingDays).label;
          if (status === 'active') return s === 'فعّال';
          if (status === 'expSoon') return s === 'قريب الانتهاء';
          if (status === 'expired') return s === 'منتهي';
          return true;
        });
      }
      if (from) list = list.filter(x => x.start >= from);
      if (to) list = list.filter(x => x.end <= to);

      render(list);
      hrToast('تم تطبيق الفلاتر (واجهة فقط).', 'info');
    }

    document.getElementById('applyFilters').addEventListener('click', applyFilters);
    document.getElementById('resetFilters').addEventListener('click', () => {
      document.getElementById('q').value = '';
      document.getElementById('status').value = 'all';
      document.getElementById('from').value = '';
      document.getElementById('to').value = '';
      render(demo);
      hrToast('تم مسح الفلاتر (واجهة فقط).', 'info');
    });
    document.getElementById('newContractBtn')?.addEventListener('click', () => hrToast('إضافة عقد: سيتم ربطها لاحقًا مع API.', 'info'));
    document.getElementById('newContractBtn2')?.addEventListener('click', () => hrToast('إضافة عقد: سيتم ربطها لاحقًا مع API.', 'info'));

    render(demo);
  </script>
</body>
</html>
