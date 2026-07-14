<?php
/*
|--------------------------------------------------------------------------
| File Name : navbar.php
| Path      : /shared/navbar.php
| Project   : My Real Estate Wallet
|--------------------------------------------------------------------------
| Description:
| Main Navigation Bar
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| ملاحظة
|--------------------------------------------------------------------------
| يفضل أن يتم استدعاء config.php قبل هذا الملف.
| مثال في index.php:
|
| require_once __DIR__ . '/config/config.php';
| require_once __DIR__ . '/shared/navbar.php';
|--------------------------------------------------------------------------
*/

if (!defined('BASE_URL')) {
    define('BASE_URL', '/');
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*
|--------------------------------------------------------------------------
| Current Page
|--------------------------------------------------------------------------
*/

$currentPage = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

/*
|--------------------------------------------------------------------------
| User Status
|--------------------------------------------------------------------------
*/

$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin    = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;

/*
|--------------------------------------------------------------------------
| Main Menu
|--------------------------------------------------------------------------
*/

$menuItems = [

    [
        'title'  => 'الرئيسية',
        'url'    => BASE_URL . 'index.php',
        'page'   => 'index.php',
        'icon'   => 'fa-solid fa-house'
    ],

    [
        'title'  => 'الخدمات',
        'url'    => BASE_URL . 'index.php#services',
        'page'   => 'index.php',
        'icon'   => 'fa-solid fa-briefcase'
    ],

    [
        'title'  => 'العقارات',
        'url'    => BASE_URL . 'index.php#properties',
        'page'   => 'index.php',
        'icon'   => 'fa-solid fa-building'
    ],

    [
        'title'  => 'المميزات',
        'url'    => BASE_URL . 'index.php#features',
        'page'   => 'index.php',
        'icon'   => 'fa-solid fa-star'
    ],

    [
        'title'  => 'من نحن',
        'url'    => BASE_URL . 'index.php#about',
        'page'   => 'index.php',
        'icon'   => 'fa-solid fa-circle-info'
    ],

    [
        'title'  => 'تواصل معنا',
        'url'    => BASE_URL . 'index.php#contact',
        'page'   => 'index.php',
        'icon'   => 'fa-solid fa-envelope'
    ]

];

?>

<header id="navbar" class="navbar">

    <div class="container">

        <!-- Logo -->

        <a href="<?= BASE_URL ?>index.php"
           class="logo"
           aria-label="الصفحة الرئيسية">

            <img
                src="<?= BASE_URL ?>assets/logos/logo-128.png"
                alt="شعار محفظتي العقارية"
                class="logo-image">

            <div class="logo-text">

                <h2>محفظتي العقارية</h2>

                <span>MY REAL ESTATE WALLET</span>

            </div>

        </a>
                <!-- =======================================================
             Desktop Navigation
        ======================================================== -->

        <nav

            class="main-nav"

            aria-label="القائمة الرئيسية">

            <ul>

                <?php foreach ($menuItems as $item): ?>

                    <?php

                    $isActive = ($currentPage === $item['page']);

                    ?>

                    <li>

                        <a

                            href="<?= htmlspecialchars($item['url']); ?>"

                            class="<?= $isActive ? 'active' : ''; ?>">

                            <?= htmlspecialchars($item['title']); ?>

                        </a>

                    </li>

                <?php endforeach; ?>

            </ul>

        </nav>

        <!-- =======================================================
             Navbar Actions
        ======================================================== -->

        <div class="navbar-actions">

            <!-- Dark Mode -->

            <button

                type="button"

                id="themeToggle"

                class="theme-toggle"

                aria-label="الوضع الليلي"

                title="الوضع الليلي">

                <i class="fa-solid fa-moon"></i>

            </button>

            <!-- Authentication -->

            <?php if ($isLoggedIn): ?>

                <a

                    href="<?= BASE_URL ?>admin/"

                    class="btn-dashboard">

                    <i class="fa-solid fa-gauge-high"></i>

                    <span>

                        لوحة التحكم

                    </span>

                </a>

                <a

                    href="<?= BASE_URL ?>users/logout.php"

                    class="btn-login">

                    <i class="fa-solid fa-right-from-bracket"></i>

                    <span>

                        تسجيل الخروج

                    </span>

                </a>

            <?php else: ?>

                <a

                    href="<?= BASE_URL ?>users/login.php"

                    class="btn-login">

                    <i class="fa-solid fa-user"></i>

                    <span>

                        تسجيل الدخول

                    </span>

                </a>

            <?php endif; ?>

            <!-- Mobile Button -->

            <button

                type="button"

                id="mobileMenuButton"

                class="mobile-menu"

                aria-label="فتح القائمة"

                aria-expanded="false"

                aria-controls="mobileNav">

                <i class="fa-solid fa-bars"></i>

            </button>

        </div>

    </div>

</header>
<!-- ==========================================================
     Mobile Overlay
=========================================================== -->

<div

    id="mobileOverlay"

    class="mobile-overlay"

    aria-hidden="true">

</div>

<!-- ==========================================================
     Mobile Navigation
=========================================================== -->

<aside

    id="mobileNav"

    class="mobile-nav"

    aria-hidden="true">

    <!-- Header -->

    <div class="mobile-nav-header">

        <a

            href="<?= BASE_URL ?>index.php"

            class="logo">

            <img

                src="<?= BASE_URL ?>assets/logos/logo-128.png"

                alt="محفظتي العقارية"

                class="logo-image">

            <div class="logo-text">

                <h2>

                    محفظتي العقارية

                </h2>

                <span>

                    MY REAL ESTATE WALLET

                </span>

            </div>

        </a>

        <button

            id="closeMobileMenu"

            class="mobile-close"

            type="button"

            aria-label="إغلاق القائمة">

            <i class="fa-solid fa-xmark"></i>

        </button>

    </div>

    <!-- Menu -->

    <nav

        class="mobile-nav-menu"

        aria-label="قائمة الهاتف">

        <ul>

            <?php foreach ($menuItems as $item): ?>

                <?php

                $isActive = ($currentPage === $item['page']);

                ?>

                <li>

                    <a

                        href="<?= htmlspecialchars($item['url']); ?>"

                        class="<?= $isActive ? 'active' : ''; ?>">

                        <i class="<?= htmlspecialchars($item['icon']); ?>"></i>

                        <span>

                            <?= htmlspecialchars($item['title']); ?>

                        </span>

                    </a>

                </li>

            <?php endforeach; ?>

        </ul>

    </nav>

    <!-- Footer -->

    <div class="mobile-nav-footer">

        <?php if ($isLoggedIn): ?>

            <a

                href="<?= BASE_URL ?>admin/"

                class="btn-dashboard">

                <i class="fa-solid fa-gauge-high"></i>

                <span>

                    لوحة التحكم

                </span>

            </a>

            <a

                href="<?= BASE_URL ?>users/logout.php"

                class="btn-login">

                <i class="fa-solid fa-right-from-bracket"></i>

                <span>

                    تسجيل الخروج

                </span>

            </a>

        <?php else: ?>

            <a

                href="<?= BASE_URL ?>users/login.php"

                class="btn-login">

                <i class="fa-solid fa-user"></i>

                <span>

                    تسجيل الدخول

                </span>

            </a>

        <?php endif; ?>

    </div>
        <!-- =======================================================
         Mobile Navigation Footer
    ======================================================== -->

    <div class="mobile-nav-bottom">

        <div class="mobile-nav-divider"></div>

        <div class="mobile-nav-buttons">

            <?php if ($isLoggedIn): ?>

                <a

                    href="<?= BASE_URL ?>admin/"

                    class="btn-dashboard mobile-btn">

                    <i class="fa-solid fa-gauge-high"></i>

                    <span>

                        لوحة التحكم

                    </span>

                </a>

                <a

                    href="<?= BASE_URL ?>users/logout.php"

                    class="btn-login mobile-btn">

                    <i class="fa-solid fa-right-from-bracket"></i>

                    <span>

                        تسجيل الخروج

                    </span>

                </a>

            <?php else: ?>

                <a

                    href="<?= BASE_URL ?>users/login.php"

                    class="btn-login mobile-btn">

                    <i class="fa-solid fa-user"></i>

                    <span>

                        تسجيل الدخول

                    </span>

                </a>

            <?php endif; ?>

        </div>

        <div class="mobile-nav-version">

            <small>

                My Real Estate Wallet

            </small>

            <small>

                Version 1.0.0

            </small>

        </div>

    </div>

</aside>

<!-- =======================================================
     End Of Navbar
======================================================== -->

<!--
===========================================================
ملاحظات للمطور

يعتمد هذا الملف على:

/assets/css/style.css

/assets/css/responsive.css

/assets/js/navigation.js

/assets/js/theme.js

Font Awesome 6

Google Font Cairo

ويفضل تحميل ملفات JavaScript قبل إغلاق body مباشرة.
===========================================================
-->