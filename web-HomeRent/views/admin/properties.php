<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>العقارات | إيجار ويب</title>

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
        <a href="properties.php" class="flex items-center gap-3 rounded-2xl px-4 py-3 bg-blue-50 text-blue-700 border border-blue-100">
          <i class="fa-solid fa-building w-5 text-blue-700"></i>
          <span class="font-bold">العقارات</span>
        </a>
        <a href="contracts.php" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-slate-600 hover:bg-slate-50 border border-transparent hover:border-slate-200">
          <i class="fa-solid fa-file-signature w-5"></i>
          <span class="font-semibold">العقود</span>
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
        <a href="properties.php" class="flex items-center gap-3 rounded-2xl px-4 py-3 bg-blue-50 text-blue-700 border border-blue-100">
          <i class="fa-solid fa-building w-5 text-blue-700"></i>
          <span class="font-bold">العقارات</span>
        </a>
        <a href="contracts.php" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-slate-600 hover:bg-slate-50 border border-transparent hover:border-slate-200">
          <i class="fa-solid fa-file-signature w-5"></i>
          <span class="font-semibold">العقود</span>
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
              <input id="globalSearch" class="w-full rounded-2xl border border-slate-200 bg-white px-12 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="بحث: اسم عقار / مدينة / حي / وحدة" />
            </div>
          </div>

          <div class="flex items-center gap-2">
            <button id="newPropertyBtn" class="hidden sm:inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-4 py-3 text-sm font-extrabold text-white hover:bg-blue-700">
              <i class="fa-solid fa-plus"></i>
              <span>إضافة عقار</span>
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
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900">العقارات</h1>
            <p class="text-slate-500 mt-1">إدارة العقارات، الوحدات، والمرفقات — بتجربة SaaS سريعة.</p>
          </div>
          <div class="flex items-center gap-2">
            <button class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-700 hover:bg-slate-50">
              <i class="fa-solid fa-file-arrow-down ml-2"></i>
              تصدير
            </button>
            <button class="rounded-2xl bg-blue-600 px-4 py-3 text-sm font-extrabold text-white hover:bg-blue-700" id="newPropertyBtn2">
              <i class="fa-solid fa-plus ml-2"></i>
              إضافة
            </button>
          </div>
        </div>

        <!-- Filters -->
        <section class="bg-white border border-slate-100 rounded-3xl p-5 hr-shadow mb-5">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div>
              <label class="text-xs font-bold text-slate-500">المدينة</label>
              <select class="mt-1 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm bg-white">
                <option>الكل</option>
                <option>الرياض</option>
                <option>جدة</option>
                <option>الدمام</option>
              </select>
            </div>
            <div>
              <label class="text-xs font-bold text-slate-500">الحي</label>
              <input class="mt-1 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm" placeholder="مثال: النرجس" />
            </div>
            <div>
              <label class="text-xs font-bold text-slate-500">نوع العقار</label>
              <select class="mt-1 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm bg-white">
                <option>الكل</option>
                <option>عمارة</option>
                <option>فيلا</option>
                <option>مجمع</option>
              </select>
            </div>
            <div>
              <label class="text-xs font-bold text-slate-500">الحالة</label>
              <select class="mt-1 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm bg-white">
                <option>الكل</option>
                <option>به شواغر</option>
                <option>مكتمل الإشغال</option>
              </select>
            </div>
          </div>
          <div class="flex flex-wrap items-center gap-2 mt-4">
            <button id="applyFilters" class="rounded-2xl bg-slate-900 px-4 py-2.5 text-sm font-extrabold text-white hover:bg-slate-800">
              تطبيق الفلاتر
            </button>
            <button id="resetFilters" class="rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50">
              مسح
            </button>
            <div class="text-xs text-slate-500">* هذه صفحة واجهة (UI) — جاهزة للربط مع API لاحقًا.</div>
          </div>
        </section>

        <!-- Table -->
        <section class="bg-white border border-slate-100 rounded-3xl hr-shadow overflow-hidden">
          <div class="p-5 flex items-center justify-between">
            <div>
              <div class="text-lg font-extrabold">قائمة العقارات</div>
              <div class="text-sm text-slate-500">عرض سريع لحالة الإشغال والوحدات.</div>
            </div>
            <div class="text-sm text-slate-500">آخر تحديث: <span id="lastUpdate">—</span></div>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
              <thead class="bg-slate-50 text-slate-600">
                <tr>
                  <th class="text-right px-6 py-4 font-extrabold">العقار</th>
                  <th class="text-right px-6 py-4 font-extrabold">الموقع</th>
                  <th class="text-right px-6 py-4 font-extrabold">الوحدات</th>
                  <th class="text-right px-6 py-4 font-extrabold">الإشغال</th>
                  <th class="text-right px-6 py-4 font-extrabold">إجراءات</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100" id="rows">
                <!-- filled by JS (demo) -->
              </tbody>
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
      };
      return `<span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-extrabold ${map[tone] || map.blue}">${text}</span>`;
    }

    // Demo dataset (replace with API)
    const demo = [
      { name: 'عمارة الرياض', city: 'الرياض', district: 'النرجس', units: 12, occupied: 10 },
      { name: 'مجمع الندى', city: 'الرياض', district: 'الندى', units: 20, occupied: 20 },
      { name: 'فيلا الرمال', city: 'الرياض', district: 'الرمال', units: 1, occupied: 0 },
    ];

    function render() {
      const tbody = document.getElementById('rows');
      tbody.innerHTML = demo.map(p => {
        const vacancy = Math.max(0, p.units - p.occupied);
        const tone = vacancy === 0 ? 'green' : vacancy >= Math.ceil(p.units * 0.35) ? 'red' : 'amber';
        const occText = `${p.occupied}/${p.units}`;
        const occBadge = vacancy === 0 ? badge('مكتمل', 'green') : badge(`شواغر: ${vacancy}`, tone);
        return `
          <tr class="hover:bg-slate-50/60">
            <td class="px-6 py-4">
              <div class="font-extrabold text-slate-900">${p.name}</div>
              <div class="text-xs text-slate-500">معرّف: —</div>
            </td>
            <td class="px-6 py-4">
              <div class="font-bold">${p.city} — ${p.district}</div>
              <div class="text-xs text-slate-500">نوع: —</div>
            </td>
            <td class="px-6 py-4">
              <div class="font-extrabold">${p.units.toLocaleString('ar-SA')}</div>
              <div class="text-xs text-slate-500">وحدة</div>
            </td>
            <td class="px-6 py-4">
              <div class="flex items-center gap-2">
                ${badge(occText, 'blue')}
                ${occBadge}
              </div>
            </td>
            <td class="px-6 py-4">
              <div class="flex flex-wrap gap-2">
                <button class="rounded-2xl border border-slate-200 bg-white px-3 py-2 text-xs font-extrabold text-slate-700 hover:bg-slate-50">تفاصيل</button>
                <button class="rounded-2xl border border-slate-200 bg-white px-3 py-2 text-xs font-extrabold text-slate-700 hover:bg-slate-50">الوحدات</button>
                <button class="rounded-2xl border border-slate-200 bg-white px-3 py-2 text-xs font-extrabold text-slate-700 hover:bg-slate-50">PDF</button>
              </div>
            </td>
          </tr>
        `;
      }).join('');
    }

    document.getElementById('applyFilters').addEventListener('click', () => hrToast('تم تطبيق الفلاتر (واجهة فقط).', 'info'));
    document.getElementById('resetFilters').addEventListener('click', () => hrToast('تم مسح الفلاتر (واجهة فقط).', 'info'));
    document.getElementById('newPropertyBtn')?.addEventListener('click', () => hrToast('إضافة عقار: سيتم ربطها لاحقًا مع API.', 'info'));
    document.getElementById('newPropertyBtn2')?.addEventListener('click', () => hrToast('إضافة عقار: سيتم ربطها لاحقًا مع API.', 'info'));

    render();
  </script>
</body>
</html>
