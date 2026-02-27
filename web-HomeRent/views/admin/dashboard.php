<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>لوحة الإدارة | إيجار ويب</title>

  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: { cairo: ['Cairo', 'ui-sans-serif', 'system-ui'] },
          colors: {
            hr: {
              bg: '#f6f7fb',
              card: '#ffffff',
              text: '#0f172a'
            }
          }
        }
      }
    }
  </script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../../assets/css/app.css" />
</head>

<body class="min-h-screen bg-hr-bg font-cairo text-slate-900">

  <!-- Mobile overlay -->
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
        <a href="dashboard.php" class="flex items-center gap-3 rounded-2xl px-4 py-3 bg-blue-50 text-blue-700 border border-blue-100">
          <i class="fa-solid fa-chart-pie w-5 text-blue-700"></i>
          <span class="font-bold">الرئيسية</span>
        </a>
        <a href="properties.php" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-slate-600 hover:bg-slate-50 border border-transparent hover:border-slate-200">
          <i class="fa-solid fa-building w-5"></i>
          <span class="font-semibold">العقارات</span>
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
        <a href="dashboard.php" class="flex items-center gap-3 rounded-2xl px-4 py-3 bg-blue-50 text-blue-700 border border-blue-100">
          <i class="fa-solid fa-chart-pie w-5 text-blue-700"></i>
          <span class="font-bold">الرئيسية</span>
        </a>
        <a href="properties.php" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-slate-600 hover:bg-slate-50 border border-transparent hover:border-slate-200">
          <i class="fa-solid fa-building w-5"></i>
          <span class="font-semibold">العقارات</span>
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
              <input id="globalSearch" class="w-full rounded-2xl border border-slate-200 bg-white px-12 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="بحث سريع: عقد / جوال / هوية / عقار / وحدة" />
            </div>
          </div>

          <div class="flex items-center gap-2">
            <button id="refreshBtn" class="hidden sm:inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-700 hover:bg-slate-50">
              <i class="fa-solid fa-arrows-rotate"></i>
              <span>تحديث</span>
            </button>

            <button class="h-11 w-11 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700" aria-label="إشعارات">
              <i class="fa-solid fa-bell"></i>
            </button>

            <div class="hidden sm:flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-3 py-2">
              <div class="text-left">
                <div id="userNameDisplay" class="text-sm font-extrabold text-slate-800">جاري التحميل…</div>
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

        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-8">
          <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900">لوحة القيادة</h1>
            <p class="text-slate-500 mt-1">نظرة سريعة على صحة النظام، المستخدمين، والعقود.</p>
          </div>
          <div class="flex items-center gap-2">
            <button class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-700 hover:bg-slate-50">
              <i class="fa-solid fa-file-arrow-down ml-2"></i>
              تصدير تقرير
            </button>
            <button class="rounded-2xl bg-blue-600 px-4 py-3 text-sm font-extrabold text-white hover:bg-blue-700">
              <i class="fa-solid fa-plus ml-2"></i>
              إضافة
            </button>
          </div>
        </div>

        <!-- KPI -->
        <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

          <div class="bg-white border border-slate-100 rounded-3xl p-5 hr-shadow">
            <div class="flex items-center justify-between">
              <div>
                <div class="text-sm font-bold text-slate-500">المستخدمون النشطون</div>
                <div id="kpiActiveUsers" class="text-3xl font-extrabold mt-2">—</div>
              </div>
              <div class="h-12 w-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <i class="fa-solid fa-users-rays"></i>
              </div>
            </div>
            <div class="mt-4 text-xs text-slate-500">آخر 30 يوم</div>
          </div>

          <div class="bg-white border border-slate-100 rounded-3xl p-5 hr-shadow">
            <div class="flex items-center justify-between">
              <div>
                <div class="text-sm font-bold text-slate-500">إجمالي المُلاك</div>
                <div id="kpiOwners" class="text-3xl font-extrabold mt-2">—</div>
              </div>
              <div class="h-12 w-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center">
                <i class="fa-solid fa-user-tie"></i>
              </div>
            </div>
            <div class="mt-4 text-xs text-slate-500">المسجّلين في النظام</div>
          </div>

          <div class="bg-white border border-slate-100 rounded-3xl p-5 hr-shadow">
            <div class="flex items-center justify-between">
              <div>
                <div class="text-sm font-bold text-slate-500">العقود النشطة</div>
                <div id="kpiActiveContracts" class="text-3xl font-extrabold mt-2">—</div>
              </div>
              <div class="h-12 w-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <i class="fa-solid fa-file-contract"></i>
              </div>
            </div>
            <div class="mt-4 text-xs text-slate-500">مستمرّة حاليًا</div>
          </div>

          <div class="bg-white border border-slate-100 rounded-3xl p-5 hr-shadow">
            <div class="flex items-center justify-between">
              <div>
                <div class="text-sm font-bold text-slate-500">إجمالي مبالغ العقود</div>
                <div id="kpiTotalAmount" class="text-2xl font-extrabold mt-2">—</div>
              </div>
              <div class="h-12 w-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center">
                <i class="fa-solid fa-money-bill-wave"></i>
              </div>
            </div>
            <div class="mt-4 text-xs text-slate-500">قيمة تراكمية</div>
          </div>

        </section>

        <!-- Panels -->
        <section class="grid grid-cols-1 xl:grid-cols-3 gap-4 mt-6">

          <!-- Important now -->
          <div class="xl:col-span-2 bg-white border border-slate-100 rounded-3xl p-5 hr-shadow">
            <div class="flex items-center justify-between mb-4">
              <div>
                <div class="text-lg font-extrabold">المهم الآن</div>
                <div class="text-sm text-slate-500">تنبيهات حرجة تحتاج مراجعة.</div>
              </div>
              <button class="text-sm font-bold text-blue-600 hover:text-blue-700">عرض الكل</button>
            </div>

            <div id="criticalList" class="space-y-3">
              <!-- Filled by JS (fallback below) -->
              <div class="flex items-start gap-3 rounded-2xl border border-slate-200 p-4">
                <div class="mt-1 h-2.5 w-2.5 rounded-full bg-amber-500"></div>
                <div class="flex-1">
                  <div class="font-bold">لا توجد بيانات بعد</div>
                  <div class="text-sm text-slate-500">عند ربط API للإحصائيات ستظهر التنبيهات هنا.</div>
                </div>
                <span class="text-xs text-slate-500">—</span>
              </div>
            </div>
          </div>

          <!-- Recent activity -->
          <div class="bg-white border border-slate-100 rounded-3xl p-5 hr-shadow">
            <div class="flex items-center justify-between mb-4">
              <div>
                <div class="text-lg font-extrabold">آخر العمليات</div>
                <div class="text-sm text-slate-500">سجل مختصر لآخر الأنشطة.</div>
              </div>
              <button class="text-sm font-bold text-blue-600 hover:text-blue-700">السجل</button>
            </div>

            <div id="activityList" class="space-y-3">
              <div class="flex items-start gap-3 rounded-2xl border border-slate-200 p-4">
                <div class="h-9 w-9 rounded-2xl bg-slate-100 text-slate-700 flex items-center justify-center">
                  <i class="fa-solid fa-clock"></i>
                </div>
                <div class="flex-1">
                  <div class="font-bold">—</div>
                  <div class="text-sm text-slate-500">سيظهر هنا آخر السداد/العقود/التنبيهات.</div>
                </div>
              </div>
            </div>
          </div>

        </section>

        <!-- Footer note -->
        <div class="mt-10 text-center text-xs text-slate-500">
          © <span id="year"></span> إيجار ويب — نسخة تجريبية UI (قابلة للربط مع API)
        </div>

      </main>
    </div>
  </div>

  <script src="../../assets/js/app.js"></script>
  <script>
    // Auth
    const auth = hrRequireAuth({ redirectTo: '../../index.html' });
    if (auth.ok) {
      document.getElementById('userNameDisplay').textContent = auth.user.name || 'مدير النظام';
    }

    document.getElementById('year').textContent = new Date().getFullYear();

    // Demo data (until API exists)
    function fmtMoney(n) {
      const v = Number(n || 0);
      return v.toLocaleString('ar-SA') + ' ر.س';
    }

    function setKpis({ activeUsers=0, owners=0, activeContracts=0, totalAmount=0 } = {}) {
      document.getElementById('kpiActiveUsers').textContent = activeUsers.toLocaleString('ar-SA');
      document.getElementById('kpiOwners').textContent = owners.toLocaleString('ar-SA');
      document.getElementById('kpiActiveContracts').textContent = activeContracts.toLocaleString('ar-SA');
      document.getElementById('kpiTotalAmount').textContent = fmtMoney(totalAmount);
    }

    // TODO: replace with real API call, e.g. GET ../../api/admin/stats.php
    function loadDashboard() {
      setKpis({ activeUsers: 0, owners: 0, activeContracts: 0, totalAmount: 0 });
    }

    document.getElementById('refreshBtn')?.addEventListener('click', () => {
      hrToast('تم تحديث البيانات (واجهة فقط حالياً).', 'info');
      loadDashboard();
    });

    loadDashboard();
  </script>
</body>
</html>
