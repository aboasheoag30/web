<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>المالية | إيجار ويب</title>

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
        <a href="contracts.php" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-slate-600 hover:bg-slate-50 border border-transparent hover:border-slate-200">
          <i class="fa-solid fa-file-signature w-5"></i>
          <span class="font-semibold">العقود</span>
        </a>
        <a href="finance.php" class="flex items-center gap-3 rounded-2xl px-4 py-3 bg-blue-50 text-blue-700 border border-blue-100">
          <i class="fa-solid fa-sack-dollar w-5 text-blue-700"></i>
          <span class="font-bold">المالية</span>
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
        <a href="contracts.php" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-slate-600 hover:bg-slate-50 border border-transparent hover:border-slate-200">
          <i class="fa-solid fa-file-signature w-5"></i>
          <span class="font-semibold">العقود</span>
        </a>
        <a href="finance.php" class="flex items-center gap-3 rounded-2xl px-4 py-3 bg-blue-50 text-blue-700 border border-blue-100">
          <i class="fa-solid fa-sack-dollar w-5 text-blue-700"></i>
          <span class="font-bold">المالية</span>
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
              <input id="globalSearch" class="w-full rounded-2xl border border-slate-200 bg-white px-12 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="بحث: عقد / مستأجر / عملية / فاتورة" />
            </div>
          </div>

          <div class="flex items-center gap-2">
            <button id="addPaymentBtn" class="hidden sm:inline-flex items-center gap-2 rounded-2xl bg-emerald-600 px-4 py-3 text-sm font-extrabold text-white hover:bg-emerald-700">
              <i class="fa-solid fa-plus"></i>
              <span>إضافة سداد</span>
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
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900">المالية</h1>
            <p class="text-slate-500 mt-1">ملخص سريع + سجل عمليات (مطالبة أحمر / سداد أخضر).</p>
          </div>
          <div class="flex items-center gap-2">
            <button class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-700 hover:bg-slate-50">
              <i class="fa-solid fa-file-arrow-down ml-2"></i>
              تصدير
            </button>
            <button class="rounded-2xl bg-emerald-600 px-4 py-3 text-sm font-extrabold text-white hover:bg-emerald-700" id="addPaymentBtn2">
              <i class="fa-solid fa-plus ml-2"></i>
              سداد
            </button>
          </div>
        </div>

        <!-- Summary cards -->
        <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-5">
          <div class="bg-white border border-slate-100 rounded-3xl p-5 hr-shadow">
            <div class="flex items-center justify-between">
              <div>
                <div class="text-sm font-bold text-slate-500">متأخرات</div>
                <div id="kpiOverdue" class="text-3xl font-extrabold mt-2">—</div>
              </div>
              <div class="h-12 w-12 rounded-2xl bg-rose-50 text-rose-700 flex items-center justify-center">
                <i class="fa-solid fa-triangle-exclamation"></i>
              </div>
            </div>
          </div>
          <div class="bg-white border border-slate-100 rounded-3xl p-5 hr-shadow">
            <div class="flex items-center justify-between">
              <div>
                <div class="text-sm font-bold text-slate-500">تستحق خلال 30 يوم</div>
                <div id="kpiDueSoon" class="text-3xl font-extrabold mt-2">—</div>
              </div>
              <div class="h-12 w-12 rounded-2xl bg-amber-50 text-amber-800 flex items-center justify-center">
                <i class="fa-solid fa-clock"></i>
              </div>
            </div>
          </div>
          <div class="bg-white border border-slate-100 rounded-3xl p-5 hr-shadow">
            <div class="flex items-center justify-between">
              <div>
                <div class="text-sm font-bold text-slate-500">مستقبلية</div>
                <div id="kpiFuture" class="text-3xl font-extrabold mt-2">—</div>
              </div>
              <div class="h-12 w-12 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center">
                <i class="fa-solid fa-calendar-days"></i>
              </div>
            </div>
          </div>
          <div class="bg-white border border-slate-100 rounded-3xl p-5 hr-shadow">
            <div class="flex items-center justify-between">
              <div>
                <div class="text-sm font-bold text-slate-500">مبالغ أخرى</div>
                <div id="kpiOther" class="text-3xl font-extrabold mt-2">—</div>
              </div>
              <div class="h-12 w-12 rounded-2xl bg-blue-50 text-blue-700 flex items-center justify-center">
                <i class="fa-solid fa-receipt"></i>
              </div>
            </div>
          </div>
        </section>

        <!-- Ledger filters -->
        <section class="bg-white border border-slate-100 rounded-3xl p-5 hr-shadow mb-5">
          <div class="grid grid-cols-1 md:grid-cols-6 gap-3">
            <div class="md:col-span-2">
              <label class="text-xs font-bold text-slate-500">بحث</label>
              <input id="q" class="mt-1 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm" placeholder="عقد / بند / مستأجر" />
            </div>
            <div>
              <label class="text-xs font-bold text-slate-500">النوع</label>
              <select id="kind" class="mt-1 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm bg-white">
                <option value="all">الكل</option>
                <option value="demand">مطالبة</option>
                <option value="payment">سداد</option>
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
            <div>
              <label class="text-xs font-bold text-slate-500">عرض</label>
              <select id="scope" class="mt-1 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm bg-white">
                <option value="all">الكل</option>
                <option value="overdue">متأخرات</option>
                <option value="duesoon">خلال 30 يوم</option>
                <option value="future">مستقبلي</option>
              </select>
            </div>
          </div>
          <div class="flex flex-wrap items-center gap-2 mt-4">
            <button id="applyFilters" class="rounded-2xl bg-slate-900 px-4 py-2.5 text-sm font-extrabold text-white hover:bg-slate-800">تطبيق</button>
            <button id="resetFilters" class="rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50">مسح</button>
            <div class="text-xs text-slate-500">* UI جاهز للربط مع API.</div>
          </div>
        </section>

        <!-- Ledger table -->
        <section class="bg-white border border-slate-100 rounded-3xl hr-shadow overflow-hidden">
          <div class="p-5 flex items-center justify-between">
            <div>
              <div class="text-lg font-extrabold">سجل العمليات</div>
              <div class="text-sm text-slate-500">مطالبة باللون الأحمر — سداد باللون الأخضر.</div>
            </div>
            <div class="text-sm text-slate-500">آخر تحديث: <span id="lastUpdate">—</span></div>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
              <thead class="bg-slate-50 text-slate-600">
                <tr>
                  <th class="text-right px-6 py-4 font-extrabold">النوع</th>
                  <th class="text-right px-6 py-4 font-extrabold">البند</th>
                  <th class="text-right px-6 py-4 font-extrabold">العقد</th>
                  <th class="text-right px-6 py-4 font-extrabold">التاريخ</th>
                  <th class="text-right px-6 py-4 font-extrabold">المبلغ</th>
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

    function fmtMoney(n) {
      const v = Number(n || 0);
      return v.toLocaleString('ar-SA') + ' ر.س';
    }
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
    function fmtDate(d) {
      return new Date(d).toLocaleDateString('ar-SA');
    }
    function daysBetween(a, b) {
      const ms = 24*60*60*1000;
      return Math.round((b.getTime() - a.getTime())/ms);
    }
    const today = new Date();

    // Demo ledger (replace with API)
    const demo = [
      { kind: 'demand', title: 'قسط رقم 3', contractNo: '1023', date: '2026-02-01', amount: 1500 },
      { kind: 'payment', title: 'سداد عام', contractNo: '1023', date: '2026-02-05', amount: 2000 },
      { kind: 'demand', title: 'فاتورة كهرباء', contractNo: '777', date: '2026-01-10', amount: 500 },
      { kind: 'demand', title: 'قسط رقم 1', contractNo: '998', date: '2026-02-25', amount: 1200 },
      { kind: 'demand', title: 'قسط رقم 2', contractNo: '1023', date: '2026-03-10', amount: 1500 },
    ];

    function classify(item) {
      if (item.kind !== 'demand') return { bucket: 'payment' };
      const d = new Date(item.date);
      const diff = daysBetween(today, d);
      if (diff < 0) return { bucket: 'overdue' };
      if (diff <= 30) return { bucket: 'duesoon' };
      return { bucket: 'future' };
    }

    function render(list) {
      const tbody = document.getElementById('rows');
      tbody.innerHTML = list.map(it => {
        const isPay = it.kind === 'payment';
        const tone = isPay ? 'green' : 'red';
        const kindLabel = isPay ? badge('سداد', 'green') : badge('مطالبة', 'red');
        return `
          <tr class="hover:bg-slate-50/60">
            <td class="px-6 py-4">${kindLabel}</td>
            <td class="px-6 py-4">
              <div class="font-extrabold text-slate-900">${it.title}</div>
              <div class="text-xs text-slate-500">—</div>
            </td>
            <td class="px-6 py-4">
              <div class="font-bold">عقد ${it.contractNo}</div>
              <div class="text-xs text-slate-500">—</div>
            </td>
            <td class="px-6 py-4">${fmtDate(it.date)}</td>
            <td class="px-6 py-4">
              <div class="font-extrabold ${isPay ? 'text-emerald-700' : 'text-rose-700'}">${fmtMoney(it.amount)}</div>
            </td>
            <td class="px-6 py-4">
              <div class="flex flex-wrap gap-2">
                <button class="rounded-2xl border border-slate-200 bg-white px-3 py-2 text-xs font-extrabold text-slate-700 hover:bg-slate-50">تفاصيل</button>
                ${isPay ? '' : '<button class="rounded-2xl bg-emerald-600 px-3 py-2 text-xs font-extrabold text-white hover:bg-emerald-700">سداد</button>'}
              </div>
            </td>
          </tr>
        `;
      }).join('');

      // KPIs
      const overdue = demo.filter(x => x.kind === 'demand' && classify(x).bucket === 'overdue').reduce((a,b)=>a+b.amount,0);
      const dueSoon = demo.filter(x => x.kind === 'demand' && classify(x).bucket === 'duesoon').reduce((a,b)=>a+b.amount,0);
      const future = demo.filter(x => x.kind === 'demand' && classify(x).bucket === 'future').reduce((a,b)=>a+b.amount,0);
      const other = demo.filter(x => x.kind === 'demand' && !String(x.title).includes('قسط')).reduce((a,b)=>a+b.amount,0);
      document.getElementById('kpiOverdue').textContent = fmtMoney(overdue);
      document.getElementById('kpiDueSoon').textContent = fmtMoney(dueSoon);
      document.getElementById('kpiFuture').textContent = fmtMoney(future);
      document.getElementById('kpiOther').textContent = fmtMoney(other);
    }

    function applyFilters() {
      const q = document.getElementById('q').value.trim().toLowerCase();
      const kind = document.getElementById('kind').value;
      const from = document.getElementById('from').value;
      const to = document.getElementById('to').value;
      const scope = document.getElementById('scope').value;

      let list = [...demo];
      if (q) list = list.filter(x => String(x.contractNo).includes(q) || String(x.title).toLowerCase().includes(q));
      if (kind !== 'all') list = list.filter(x => x.kind === kind);
      if (from) list = list.filter(x => x.date >= from);
      if (to) list = list.filter(x => x.date <= to);
      if (scope !== 'all') list = list.filter(x => classify(x).bucket === scope);
      render(list);
      hrToast('تم تطبيق الفلاتر (واجهة فقط).', 'info');
    }

    document.getElementById('applyFilters').addEventListener('click', applyFilters);
    document.getElementById('resetFilters').addEventListener('click', () => {
      document.getElementById('q').value = '';
      document.getElementById('kind').value = 'all';
      document.getElementById('from').value = '';
      document.getElementById('to').value = '';
      document.getElementById('scope').value = 'all';
      render(demo);
      hrToast('تم مسح الفلاتر (واجهة فقط).', 'info');
    });
    document.getElementById('addPaymentBtn')?.addEventListener('click', () => hrToast('إضافة سداد: سيتم ربطها لاحقًا مع API.', 'info'));
    document.getElementById('addPaymentBtn2')?.addEventListener('click', () => hrToast('إضافة سداد: سيتم ربطها لاحقًا مع API.', 'info'));

    render(demo);
  </script>
</body>
</html>
