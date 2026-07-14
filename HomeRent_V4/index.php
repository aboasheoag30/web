```php
<?php
/*
|--------------------------------------------------------------------------
| File Name : index.php
| Path      : /index.php
| Project   : Real Estate Management System
| Version   : 1.0.0
|--------------------------------------------------------------------------
*/
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>

    <?php require_once 'shared/header.php'; ?>

</head>

<body>

    <!-- بداية شريط التنقل -->
    <?php require_once 'shared/navbar.php'; ?>
    <!-- نهاية شريط التنقل -->

    <main>

        <!-- Hero -->
        <?php require_once 'shared/hero.php'; ?>

        <!-- Statistics -->
        <?php require_once 'shared/statistics.php'; ?>

        <!-- Services -->
        <?php require_once 'shared/services.php'; ?>

        <!-- Features -->
        <?php require_once 'shared/features.php'; ?>

        <!-- Latest Properties -->
        <?php require_once 'shared/properties.php'; ?>

        <!-- Testimonials -->
        <?php require_once 'shared/testimonials.php'; ?>

        <!-- Contact -->
        <?php require_once 'shared/contact.php'; ?>

    </main>

    <!-- Footer -->
    <?php require_once 'shared/footer.php'; ?>

</body>

</html>
```
