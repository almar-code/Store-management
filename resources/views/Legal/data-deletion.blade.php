@extends('Legal.layout')

@section('title', $lang === 'ar' ? 'حذف بيانات المستخدم' : 'User Data Deletion')
@section('icon', 'bi-trash3')


@section('content')

@if ($lang === 'ar')

    <p class="legal-intro">
        يحق لك في أي وقت طلب حذف حسابك وجميع بياناتك من متجر Nice Abayas، سواء سجلت بالبريد الإلكتروني أو عبر جوجل أو فيسبوك.
    </p>

    <section>
        <h2>طريقة طلب الحذف</h2>
        <ol class="legal-steps">
            <li>أرسل رسالة إلى بريدنا <a href="mailto:{{ $contactEmail }}?subject=طلب حذف البيانات">{{ $contactEmail }}</a>.</li>
            <li>اكتب في عنوان الرسالة: <strong>طلب حذف البيانات</strong>.</li>
            <li>اذكر البريد الإلكتروني المرتبط بحسابك في التطبيق.</li>
            <li>سنؤكد استلام طلبك ونحذف بياناتك خلال 30 يوماً كحد أقصى.</li>
        </ol>
    </section>

    <section>
        <h2>البيانات التي يتم حذفها</h2>
        <ul>
            <li>الحساب وبيانات الدخول.</li>
            <li>الاسم والبريد الإلكتروني وصورة الحساب ورقم الجوال.</li>
            <li>عناوين التوصيل والمفضلة وسلة المشتريات.</li>
        </ul>
        <p class="legal-note">
            قد نحتفظ بسجلات الطلبات والفواتير المكتملة للمدة التي يفرضها القانون فقط، ثم تُحذف.
        </p>
    </section>

    <section>
        <h2>إزالة ربط حسابك في فيسبوك</h2>
        <ol class="legal-steps">
            <li>افتح فيسبوك ثم <strong>الإعدادات والخصوصية</strong> ثم <strong>الإعدادات</strong>.</li>
            <li>اختر <strong>التطبيقات ومواقع الويب</strong>.</li>
            <li>ابحث عن <strong>Nice Login</strong> واضغط <strong>إزالة</strong>.</li>
        </ol>
    </section>

    <section class="legal-contact">
        <h2>التواصل معنا</h2>
        <p>لأي استفسار عن حذف البيانات راسلنا على:</p>
        <a href="mailto:{{ $contactEmail }}"><i class="bi bi-envelope"></i> {{ $contactEmail }}</a>
    </section>

@else

    <p class="legal-intro">
        You can request deletion of your account and all your data from Nice Abayas at any time, whether you signed up by email, Google or Facebook.
    </p>

    <section>
        <h2>How to Request Deletion</h2>
        <ol class="legal-steps">
            <li>Send an email to <a href="mailto:{{ $contactEmail }}?subject=Data Deletion Request">{{ $contactEmail }}</a>.</li>
            <li>Use the subject: <strong>Data Deletion Request</strong>.</li>
            <li>Include the email address linked to your account.</li>
            <li>We will confirm your request and delete your data within 30 days at most.</li>
        </ol>
    </section>

    <section>
        <h2>Data That Will Be Deleted</h2>
        <ul>
            <li>Your account and login details.</li>
            <li>Your name, email, profile picture and phone number.</li>
            <li>Your delivery addresses, favorites and cart.</li>
        </ul>
        <p class="legal-note">
            Completed order and invoice records may be kept only for the period required by law, then deleted.
        </p>
    </section>

    <section>
        <h2>Remove the App from Facebook</h2>
        <ol class="legal-steps">
            <li>Open Facebook, go to <strong>Settings &amp; Privacy</strong> then <strong>Settings</strong>.</li>
            <li>Select <strong>Apps and Websites</strong>.</li>
            <li>Find <strong>Nice Login</strong> and tap <strong>Remove</strong>.</li>
        </ol>
    </section>

    <section class="legal-contact">
        <h2>Contact Us</h2>
        <p>For any question about data deletion, email us at:</p>
        <a href="mailto:{{ $contactEmail }}"><i class="bi bi-envelope"></i> {{ $contactEmail }}</a>
    </section>

@endif

@endsection
