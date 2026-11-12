@extends('Legal.layout')

@section('title', $lang === 'ar' ? 'سياسة الخصوصية' : 'Privacy Policy')
@section('icon', 'bi-shield-lock')

@section('content')

@if ($lang === 'ar')

    <p class="legal-intro">
        نحن في متجر Nice Abayas نحترم خصوصيتك. توضح هذه الصفحة البيانات التي نجمعها عند استخدامك لتطبيق المتجر، وكيف نستخدمها ونحميها.
    </p>

    <section>
        <h2>البيانات التي نجمعها</h2>
        <ul>
            <li>الاسم والبريد الإلكتروني وصورة الحساب، عند التسجيل بالبريد أو عبر جوجل أو فيسبوك.</li>
            <li>رقم الجوال وعناوين التوصيل التي تضيفها بنفسك.</li>
            <li>الطلبات والمفضلة وسلة المشتريات.</li>
        </ul>
    </section>

    <section>
        <h2>كيف نستخدم بياناتك</h2>
        <ul>
            <li>إنشاء حسابك وتسجيل دخولك.</li>
            <li>تنفيذ الطلبات وتوصيلها والتواصل معك بخصوصها.</li>
            <li>تحسين تجربتك داخل التطبيق.</li>
        </ul>
    </section>

    <section>
        <h2>تسجيل الدخول عبر فيسبوك وجوجل</h2>
        <p>
            عند التسجيل عبر فيسبوك أو جوجل نحصل فقط على اسمك وبريدك الإلكتروني وصورة حسابك. لا نصل إلى كلمة المرور أو المنشورات أو قائمة الأصدقاء، ولا ننشر أي شيء باسمك.
        </p>
    </section>

    <section>
        <h2>مشاركة البيانات</h2>
        <p>
            لا نبيع بياناتك ولا نشاركها مع أي جهة لأغراض إعلانية. تُشارك بيانات التوصيل فقط مع جهة الشحن لإيصال طلبك، وبيانات الدفع تتم عبر مزود الدفع مباشرة ولا نحتفظ بها.
        </p>
    </section>

    <section>
        <h2>حماية البيانات</h2>
        <p>
            تُحفظ بياناتك في خوادم آمنة ومشفرة، ولا يصل إليها إلا المخولون بإدارة المتجر.
        </p>
    </section>

    <section>
        <h2>حقوقك</h2>
        <p>
            يحق لك تعديل بياناتك من داخل التطبيق، أو طلب حذف حسابك وكل بياناتك في أي وقت.
            <a href="{{ url('/data-deletion') }}?lang=ar">طريقة حذف البيانات</a>
        </p>
    </section>

    <section class="legal-contact">
        <h2>التواصل معنا</h2>
        <p>لأي استفسار عن الخصوصية راسلنا على:</p>
        <a href="mailto:{{ $contactEmail }}"><i class="bi bi-envelope"></i> {{ $contactEmail }}</a>
    </section>

@else

    <p class="legal-intro">
        At Nice Abayas we respect your privacy. This page explains what data we collect when you use our store app, and how we use and protect it.
    </p>

    <section>
        <h2>Data We Collect</h2>
        <ul>
            <li>Your name, email and profile picture when you sign up by email, Google or Facebook.</li>
            <li>Phone number and delivery addresses you add yourself.</li>
            <li>Your orders, favorites and cart.</li>
        </ul>
    </section>

    <section>
        <h2>How We Use Your Data</h2>
        <ul>
            <li>To create your account and sign you in.</li>
            <li>To process and deliver your orders and contact you about them.</li>
            <li>To improve your experience in the app.</li>
        </ul>
    </section>

    <section>
        <h2>Facebook and Google Login</h2>
        <p>
            When you sign in with Facebook or Google we only receive your name, email and profile picture. We never access your password, posts or friends list, and we never post on your behalf.
        </p>
    </section>

    <section>
        <h2>Data Sharing</h2>
        <p>
            We do not sell your data or share it for advertising. Delivery details are shared only with the shipping provider to deliver your order. Payments are handled directly by the payment provider and we do not store card details.
        </p>
    </section>

    <section>
        <h2>Data Protection</h2>
        <p>
            Your data is stored on secure, encrypted servers and is only accessible to authorized store staff.
        </p>
    </section>

    <section>
        <h2>Your Rights</h2>
        <p>
            You can edit your data inside the app, or request deletion of your account and all your data at any time.
            <a href="{{ url('/data-deletion') }}?lang=en">How to delete your data</a>
        </p>
    </section>

    <section class="legal-contact">
        <h2>Contact Us</h2>
        <p>For any privacy question, email us at:</p>
        <a href="mailto:{{ $contactEmail }}"><i class="bi bi-envelope"></i> {{ $contactEmail }}</a>
    </section>

@endif

@endsection
