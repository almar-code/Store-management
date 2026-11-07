@extends('Layouts.master')

@section('link')
    <link href="{{ asset('assets/css/login.css') }}?v={{ time() }}" rel="stylesheet">
    <link href="{{ secure_asset('assets/css/login.css') }}?v={{ time() }}" rel="stylesheet">
@endsection

@section('content')
@php
    // {period} يُستبدل بالفترة المختارة، والتقارير التي لا تحتويه تعمل على كل البيانات
    $readyReports = [
        ['icon' => 'bi-box-seam', 'color' => '#00695c', 'title' => 'تقرير المنتجات',
         'desc' => 'المنتجات المضافة مع السعر والخصم والفئة',
         'query' => 'تقرير بالمنتجات المضافة {period} مع السعر والخصم والفئة لكل منتج'],

        ['icon' => 'bi-tags', 'color' => '#6a1b9a', 'title' => 'تقرير الفئات',
         'desc' => 'جميع الفئات مع القسم الرئيسي لكل فئة',
         'query' => 'تقرير بجميع الفئات مع القسم الرئيسي لكل فئة'],

        ['icon' => 'bi-diagram-3', 'color' => '#283593', 'title' => 'الفئات مع إجمالي المنتجات',
         'desc' => 'عدد المنتجات وإجمالي أسعارها في كل فئة',
         'query' => 'تقرير بالفئات مع عدد المنتجات وإجمالي أسعار المنتجات في كل فئة'],

        ['icon' => 'bi-grid-3x3-gap', 'color' => '#00838f', 'title' => 'تقرير الأقسام',
         'desc' => 'الأقسام الرئيسية مع عدد الفئات والمنتجات',
         'query' => 'تقرير بالأقسام الرئيسية مع عدد الفئات وعدد المنتجات في كل قسم'],

        ['icon' => 'bi-graph-up-arrow', 'color' => '#2e7d32', 'title' => 'تقرير المبيعات',
         'desc' => 'عدد الطلبات وإجمالي المبيعات ومتوسط الطلب',
         'query' => 'تقرير المبيعات {period}: عدد الطلبات وإجمالي المبيعات ومتوسط قيمة الطلب'],

        ['icon' => 'bi-bag-check', 'color' => '#ef6c00', 'title' => 'الطلبات حسب الحالة',
         'desc' => 'الطلبات مجمعة حسب الحالة مع العدد والإجمالي',
         'query' => 'تقرير بالطلبات {period} مجمعة حسب حالة الطلب مع عدد الطلبات وإجمالي قيمتها لكل حالة'],

        ['icon' => 'bi-x-octagon', 'color' => '#c62828', 'title' => 'الطلبات الملغاة',
         'desc' => 'الطلبات الملغاة مع العميل والقيمة',
         'query' => 'تقرير بالطلبات الملغاة {period} مع اسم العميل وقيمة الطلب وتاريخه'],

        ['icon' => 'bi-person-plus', 'color' => '#1565c0', 'title' => 'العملاء الجدد',
         'desc' => 'العملاء المسجلون خلال الفترة',
         'query' => 'تقرير بالعملاء المسجلين {period} مع البريد الإلكتروني ورقم الهاتف وتاريخ التسجيل'],

        ['icon' => 'bi-trophy', 'color' => '#f9a825', 'title' => 'أفضل العملاء',
         'desc' => 'أكثر العملاء شراءً وإجمالي مشترياتهم',
         'query' => 'أكثر 20 عميلاً شراءً {period} مع عدد الطلبات وإجمالي المشتريات لكل عميل'],

        ['icon' => 'bi-geo-alt', 'color' => '#ad1457', 'title' => 'أكثر المناطق طلباً',
         'desc' => 'المدن والمناطق الأعلى طلباً للمنتجات',
         'query' => 'تقرير بأكثر المدن والمناطق طلباً للمنتجات {period} مع عدد الطلبات وإجمالي المبيعات لكل منطقة'],

        ['icon' => 'bi-fire', 'color' => '#d84315', 'title' => 'الأكثر مبيعاً',
         'desc' => 'المنتجات الأعلى مبيعاً مع الكمية والإيراد',
         'query' => 'أكثر 20 منتجاً مبيعاً {period} مع الكمية المباعة وإجمالي الإيرادات لكل منتج'],

        ['icon' => 'bi-hourglass-split', 'color' => '#6d4c41', 'title' => 'المنتجات الراكدة',
         'desc' => 'المنتجات التي لم تُبع أو الأقل مبيعاً',
         'query' => 'تقرير بالمنتجات التي لم تُبع أو كانت الأقل مبيعاً {period}'],

        ['icon' => 'bi-percent', 'color' => '#00897b', 'title' => 'المنتجات المخفضة',
         'desc' => 'السعر قبل وبعد الخصم ونسبة الخصم',
         'query' => 'تقرير بالمنتجات التي عليها خصم مع السعر قبل الخصم وبعده ونسبة الخصم'],

        ['icon' => 'bi-star-half', 'color' => '#ffb300', 'title' => 'تقييمات المنتجات',
         'desc' => 'التعليقات ومتوسط النجوم لكل منتج',
         'query' => 'تقرير بتقييمات وتعليقات المنتجات {period} مع عدد التعليقات ومتوسط النجوم لكل منتج'],

        ['icon' => 'bi-emoji-smile', 'color' => '#43a047', 'title' => 'تحليل آراء العملاء',
         'desc' => 'التعليقات الإيجابية والسلبية والمحايدة',
         'query' => 'تقرير بتحليل مشاعر التعليقات {period}: عدد التعليقات الإيجابية والسلبية والمحايدة لكل منتج'],

        ['icon' => 'bi-heart', 'color' => '#e53935', 'title' => 'الأكثر في المفضلة',
         'desc' => 'المنتجات الأكثر إضافة إلى المفضلة',
         'query' => 'تقرير بأكثر المنتجات إضافة إلى المفضلة مع عدد العملاء لكل منتج'],

        ['icon' => 'bi-cart-x', 'color' => '#5e35b1', 'title' => 'السلات المتروكة',
         'desc' => 'منتجات في سلات العملاء لم تُطلب',
         'query' => 'تقرير بالمنتجات الموجودة في سلات العملاء ولم يتم طلبها مع الكمية واسم العميل'],

        ['icon' => 'bi-palette', 'color' => '#8e24aa', 'title' => 'الألوان والمقاسات',
         'desc' => 'الألوان والمقاسات المتوفرة لكل منتج',
         'query' => 'تقرير بالمنتجات مع الألوان والمقاسات المتوفرة لكل منتج'],

        ['icon' => 'bi-megaphone', 'color' => '#0277bd', 'title' => 'الإعلانات والعروض',
         'desc' => 'حالة كل إعلان وتاريخ انتهائه',
         'query' => 'تقرير بالإعلانات والعروض الترويجية مع نوع الإعلان وحالته وتاريخ انتهائه'],

        ['icon' => 'bi-people', 'color' => '#455a64', 'title' => 'مستخدمو النظام',
         'desc' => 'مستخدمو لوحة التحكم وصلاحياتهم',
         'query' => 'تقرير بمستخدمي لوحة التحكم وصلاحياتهم وحالة كل صلاحية'],
    ];
@endphp

    <style>
        .rpt-period { background: #fff; border-radius: 14px; box-shadow: 0 4px 20px rgba(0, 0, 0, .06); padding: 18px 20px; margin: 18px 8px 8px; }
        .rpt-period-row { display: flex; flex-wrap: wrap; align-items: flex-end; gap: 14px; justify-content: space-between; }
        .rpt-dates { display: flex; flex-wrap: wrap; gap: 12px; }
        .rpt-dates label { display: block; font-size: .82rem; color: #666; margin-bottom: 4px; }
        .rpt-dates input { border-radius: 8px; min-width: 170px; }
        .rpt-dates input:focus { border-color: #00695c; box-shadow: 0 0 0 .2rem rgba(0, 105, 92, .15); }
        .rpt-quick { display: flex; flex-wrap: wrap; gap: 8px; }
        .rpt-quick button { border: 1px solid #cfe3e0; background: #f3f8f7; color: #00695c; border-radius: 6px; padding: 7px 14px; font-size: .85rem; cursor: pointer; transition: .2s; }
        .rpt-quick button:hover, .rpt-quick button.active { background: #00695c; color: #fff; border-color: #00695c; }

        .rpt-ready { position: relative; overflow: hidden; background: #fff; border-radius: 14px; box-shadow: 0 4px 20px rgba(0, 0, 0, .06); padding: 20px; height: 100%; cursor: pointer; transition: .2s; text-align: right; border: 1px solid transparent; }
        .rpt-ready:hover { transform: translateY(-3px); box-shadow: 0 8px 28px rgba(0, 0, 0, .1); border-color: #cfe3e0; }
        .rpt-ready-icon { width: 46px; height: 46px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.35rem; margin-bottom: 12px; }
        .rpt-ready-title { font-size: 1rem; font-weight: 700; color: #222; margin-bottom: 6px; }
        .rpt-ready-desc { font-size: .85rem; color: #777; margin-bottom: 12px; min-height: 2.6em; }
        .rpt-ready-badge { display: inline-block; font-size: .72rem; padding: 3px 8px; border-radius: 6px; background: #f1f3f5; color: #666; }
        .rpt-ready-badge.dated { background: #e0f2f1; color: #00695c; }

        /* طبقة الإنشاء فوق البطاقة */
        .rpt-overlay { position: absolute; inset: 0; background: rgba(255, 255, 255, .88); backdrop-filter: blur(2px); display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 10px; color: #00695c; font-weight: 600; z-index: 2; }
        .rpt-overlay .spinner-border { width: 2rem; height: 2rem; color: #00695c; }
        .rpt-cancel { margin-top: 4px; background: #fff; color: #c62828; border: 1px solid #f3c7c7; border-radius: 6px; padding: 5px 16px; font-size: .82rem; font-weight: 600; cursor: pointer; transition: .2s; }
        .rpt-cancel:hover { background: #c62828; color: #fff; border-color: #c62828; }
        .rpt-grid-busy .rpt-ready { pointer-events: none; }
        .rpt-grid-busy .rpt-ready .rpt-overlay { pointer-events: auto; cursor: default; }
        .rpt-grid-busy .rpt-ready:not(.loading) { opacity: .55; }
    </style>

    <div class="container my-4" dir="rtl">
        <div class="container">
            <div class="controls-container">
                <div class="section-ar">
                    <div class="section-title" style="margin-bottom: 0px;">
                        <h3 style="font-size: 20px">قائمة <span class="orange-text">التقارير الجاهزة</span></h3>
                    </div>
                </div>

                <div class="search-input-container">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" class="search-input" placeholder="Search" id="sectionSearch">
                </div>

                <a href="{{ route('reports.custom') }}" class="btn-add-product shadow-sm">
                    تقرير مخصص
                    <i class="bi bi-pencil-square ms-2"></i>
                </a>
            </div>
        </div>

        {{-- اختيار الفترة --}}
        <div class="rpt-period">
            <div class="rpt-period-row">
                <div class="rpt-dates">
                    <div>
                        <label for="dateFrom">من تاريخ</label>
                        <input type="date" id="dateFrom" class="form-control">
                    </div>
                    <div>
                        <label for="dateTo">إلى تاريخ</label>
                        <input type="date" id="dateTo" class="form-control">
                    </div>
                </div>
                <div class="rpt-quick" id="quickPeriods">
                    <button type="button" data-period="day">تقرير يومي</button>
                    <button type="button" data-period="week">آخر أسبوع</button>
                    <button type="button" data-period="month">آخر شهر</button>
                    <button type="button" data-period="year">آخر سنة</button>
                </div>
            </div>
        </div>

        {{-- البطاقات --}}
        <div class="row g-3 px-2 mt-1" id="readyGrid">
            @foreach ($readyReports as $report)
                @php $dated = str_contains($report['query'], '{period}'); @endphp
                <div class="col-12 col-md-6 col-xl-3 card-item">
                    <div class="rpt-ready" role="button" tabindex="0"
                        data-query="{{ $report['query'] }}">
                        <div class="rpt-ready-icon" style="background: {{ $report['color'] }}1a; color: {{ $report['color'] }};">
                            <i class="bi {{ $report['icon'] }}"></i>
                        </div>
                        <div class="rpt-ready-title card-title">{{ $report['title'] }}</div>
                        <div class="rpt-ready-desc card-text">{{ $report['desc'] }}</div>
                        <span class="rpt-ready-badge {{ $dated ? 'dated' : '' }}">
                            <i class="bi {{ $dated ? 'bi-calendar-range' : 'bi-database' }}"></i>
                            {{ $dated ? 'حسب الفترة المحددة' : 'كل البيانات' }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script>
        (function () {
            const routes = {
                generate: "{{ route('report.generate') }}",
                custom: "{{ route('reports.custom') }}"
            };
            const csrf = "{{ csrf_token() }}";
            const dateFrom = document.getElementById('dateFrom');
            const dateTo = document.getElementById('dateTo');
            const grid = document.getElementById('readyGrid');
            const quickButtons = document.querySelectorAll('#quickPeriods button');
            let busy = false;
            let controller = null; // لإلغاء الطلب الجاري

            function showError(text) {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    timer: 3000,
                    width: '250px',
                    timerProgressBar: true,
                    showConfirmButton: false,
                    showCloseButton: true,
                    text: text,
                    customClass: {
                        popup: 'compact-alert'
                    }
                });
            }

            // تنسيق التاريخ المحلي بدون تأثير فرق التوقيت
            function fmt(d) {
                const pad = n => String(n).padStart(2, '0');
                return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());
            }

            function setPeriod(type) {
                const end = new Date();
                const start = new Date();

                if (type === 'week') start.setDate(start.getDate() - 6);
                if (type === 'month') start.setMonth(start.getMonth() - 1);
                if (type === 'year') start.setFullYear(start.getFullYear() - 1);

                dateFrom.value = fmt(start);
                dateTo.value = fmt(end);

                quickButtons.forEach(b => b.classList.toggle('active', b.dataset.period === type));
            }

            quickButtons.forEach(function (btn) {
                btn.addEventListener('click', () => setPeriod(btn.dataset.period));
            });

            [dateFrom, dateTo].forEach(function (input) {
                input.addEventListener('change', () => quickButtons.forEach(b => b.classList.remove('active')));
            });

            setPeriod('month');

            function buildQuery(template) {
                if (!template.includes('{period}')) return template;

                const from = dateFrom.value;
                const to = dateTo.value;
                const period = from === to
                    ? 'ليوم ' + from
                    : 'خلال الفترة من ' + from + ' إلى ' + to;

                return template.replace('{period}', period);
            }

            function validatePeriod() {
                if (!dateFrom.value || !dateTo.value) {
                    showError('يرجى تحديد تاريخ البداية والنهاية');
                    return false;
                }
                if (dateFrom.value > dateTo.value) {
                    showError('تاريخ البداية يجب أن يكون قبل تاريخ النهاية');
                    return false;
                }
                return true;
            }

            function setLoading(card, state) {
                busy = state;
                grid.classList.toggle('rpt-grid-busy', state);
                card.classList.toggle('loading', state);

                const existing = card.querySelector('.rpt-overlay');
                if (state && !existing) {
                    const overlay = document.createElement('div');
                    overlay.className = 'rpt-overlay';
                    overlay.innerHTML = '<div class="spinner-border" role="status" aria-hidden="true"></div>'
                        + '<span>جاري الإنشاء...</span>'
                        + '<button type="button" class="rpt-cancel"><i class="bi bi-x-lg"></i> إلغاء</button>';

                    // منع وصول الضغط إلى البطاقة نفسها
                    overlay.addEventListener('click', e => e.stopPropagation());
                    overlay.addEventListener('keydown', e => e.stopPropagation());

                    overlay.querySelector('.rpt-cancel').addEventListener('click', function () {
                        if (controller) controller.abort();
                    });

                    card.appendChild(overlay);
                } else if (!state && existing) {
                    existing.remove();
                }
            }

            async function generate(card) {
                if (busy) return;

                const template = card.dataset.query;
                if (template.includes('{period}') && !validatePeriod()) return;

                const query = buildQuery(template);
                controller = new AbortController();
                setLoading(card, true);

                try {
                    const res = await fetch(routes.generate, {
                        method: 'POST',
                        signal: controller.signal,
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrf
                        },
                        body: JSON.stringify({ report_query: query })
                    });

                    let data = {};
                    try { data = await res.json(); } catch (e) {}

                    if (!res.ok || data.success === false || !data.pdf_url) {
                        throw new Error(data.message || 'حدث خطأ أثناء إنشاء التقرير');
                    }

                    // عرض التقرير في صفحة التقرير المخصص (حفظ، تحميل، تعديل)
                    try {
                        sessionStorage.setItem('pendingReport', JSON.stringify({
                            title: data.title,
                            file: data.file,
                            url: data.pdf_url,
                            query: query
                        }));
                        window.location.href = routes.custom;
                    } catch (e) {
                        window.location.href = data.pdf_url;
                    }
                } catch (err) {
                    setLoading(card, false);

                    // الإلغاء من المدير لا يحتاج رسالة خطأ
                    if (err.name !== 'AbortError') {
                        showError(err.message);
                    }
                } finally {
                    controller = null;
                }
            }

            grid.querySelectorAll('.rpt-ready').forEach(function (card) {
                card.addEventListener('click', () => generate(card));
                card.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        generate(card);
                    }
                });
            });
        })();
    </script>
@endsection