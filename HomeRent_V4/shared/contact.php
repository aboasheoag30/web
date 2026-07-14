```php
<?php
/*
|--------------------------------------------------------------------------
| File Name : contact.php
| Path      : /shared/contact.php
|--------------------------------------------------------------------------
*/
?>

<section class="contact">

    <div class="container">

        <div class="section-title">

            <h2>تواصل معنا</h2>

            <p>

                يسعدنا استقبال استفساراتكم وملاحظاتكم.

            </p>

        </div>

        <form action="#" method="post" class="contact-form">

            <div class="form-group">

                <label>الاسم الكامل</label>

                <input
                    type="text"
                    name="name"
                    placeholder="أدخل الاسم"
                    required>

            </div>

            <div class="form-group">

                <label>البريد الإلكتروني</label>

                <input
                    type="email"
                    name="email"
                    placeholder="example@email.com"
                    required>

            </div>

            <div class="form-group">

                <label>الموضوع</label>

                <input
                    type="text"
                    name="subject"
                    placeholder="عنوان الرسالة">

            </div>

            <div class="form-group">

                <label>الرسالة</label>

                <textarea
                    name="message"
                    rows="6"
                    placeholder="اكتب رسالتك هنا..."
                    required></textarea>

            </div>

            <button
                type="submit"
                class="btn-primary">

                <i class="fa-solid fa-paper-plane"></i>

                إرسال الرسالة

            </button>

        </form>

    </div>

</section>
```
