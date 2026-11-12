<!DOCTYPE html>
<html lang="{{ $lang }}" dir="{{ $lang === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Nice Abayas</title>
    <link href="{{ asset('assets/img/logo.png') }}" rel="icon">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="{{ asset('assets/css/legal.css') }}?v=1" rel="stylesheet">
</head>
<body class="legal-page">

    <div class="legal-container">

        {{-- الرأس --}}
        <header class="legal-header">
            <div class="legal-brand">
                <img src="{{ asset('assets/img/logo.png') }}" alt="Nice">
                <span>Nice Abayas</span>
            </div>

            {{-- تبديل اللغة --}}
            <a class="legal-lang" href="?lang={{ $lang === 'ar' ? 'en' : 'ar' }}">
                <i class="bi bi-translate"></i>
                {{ $lang === 'ar' ? 'English' : 'العربية' }}
            </a>
        </header>

        {{-- المحتوى --}}
        <main class="legal-card">
            <div class="legal-title">
                <i class="bi @yield('icon')"></i>
                <div>
                    <h1>@yield('title')</h1>
                    <p>{{ $lang === 'ar' ? 'آخر تحديث: نوفمبر 2026' : 'Last updated: November 2026' }}</p>
                </div>
            </div>

            @yield('content')
        </main>

        {{-- التذييل --}}
        <footer class="legal-footer">
            <a href="{{ url('/privacy-policy') }}?lang={{ $lang }}">
                {{ $lang === 'ar' ? 'سياسة الخصوصية' : 'Privacy Policy' }}
            </a>
            <span>|</span>
            <a href="{{ url('/data-deletion') }}?lang={{ $lang }}">
                {{ $lang === 'ar' ? 'حذف البيانات' : 'Data Deletion' }}
            </a>
            <p>&copy; {{ date('Y') }} Nice Abayas</p>
        </footer>

    </div>

</body>
</html>