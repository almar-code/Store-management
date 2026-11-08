@extends('Layouts.master')

@section('link')
    <link href="{{ secure_asset('assets/css/chart.css') }}?v={{ time() }}" rel="stylesheet">
    <link href="{{ asset('assets/css/chart.css') }}?v={{ time() }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <style>
        /* شبكات إضافية بنفس أسلوب الرئيسية */
        .perf-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 25px; margin-top: 25px; direction: rtl; }
        .perf-grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 25px; margin-top: 25px; direction: rtl; }
        .perf-full { margin-top: 25px; direction: rtl; }
        .grid-layout, .bottom-grid { margin-top: 25px; }
        .bottom-grid { direction: ltr; }

        /* شارة صغيرة بجانب الرقم */
        .chip { font-size: 12px; font-weight: 700; padding: 2px 8px; border-radius: 10px; background: #ecfdf5; color: #14b8a6; white-space: nowrap; }
        .chip.purple { background: #f1f0fe; color: #6c5ce7; }

        /* قائمة الترتيب (أفضل وأسوأ 10) */
        .rank-list { margin-top: 15px; }
        .rank-item { display: flex; align-items: center; gap: 12px; padding: 9px 0; border-bottom: 1px solid #f3f4f6; }
        .rank-item:last-child { border-bottom: none; }
        .rank-num { width: 26px; height: 26px; flex-shrink: 0; border-radius: 8px; background: #f1f0fe; color: #6c5ce7; font-size: 12px; font-weight: 700; display: flex; align-items: center; justify-content: center; }
        .rank-num.red { background: #fef2f2; color: #ef4444; }
        .rank-item img { width: 40px; height: 40px; flex-shrink: 0; object-fit: cover; border-radius: 8px; background: #f1f2f6; }
        .rank-body { flex: 1; min-width: 0; }
        .rank-name { font-size: 13px; font-weight: 700; color: #1f2937; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .rank-sub { font-size: 11px; color: #95a5a6; margin-top: 2px; }
        .rank-body .bar { margin: 6px 0 0; }
        .rank-score { font-size: 14px; font-weight: 700; color: #1f2937; flex-shrink: 0; }

        /* المخططات */
        .chart-md { position: relative; width: 100%; height: 220px; margin-top: 15px; }
        .chart-empty { display: flex; align-items: center; justify-content: center; height: 100%; min-height: 80px; color: #95a5a6; font-size: 12px; font-weight: bold; text-align: center; }
        .panel-head { display: flex; justify-content: space-between; align-items: center; gap: 10px; flex-wrap: wrap; }
        .panel-link { font-size: 12px; color: #6c5ce7; text-decoration: none; font-weight: 600; }
        .color-dot { display: inline-block; width: 12px; height: 12px; border-radius: 50%; border: 1px solid #e5e7eb; margin-left: 6px; vertical-align: middle; }
        .method-badge { display: inline-flex; align-items: center; gap: 6px; background: #f1f0fe; color: #6c5ce7; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; }
        .status-badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; color: #fff; white-space: nowrap; }

        /* التقرير الذكي */
        .ai-btn { background: #6c5ce7; color: #fff; border: none; padding: 8px 16px; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; }
        .ai-btn:disabled { opacity: .6; cursor: wait; }
        .ai-report { margin-top: 15px; font-size: 14px; line-height: 1.9; color: #2d3436; }
        .ai-report h4 { font-size: 15px; font-weight: 700; color: #6c5ce7; margin: 18px 0 8px; }
        .ai-report ul { padding-right: 20px; margin: 0; }

        /* الفلترة والجدول */
        .filters { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 15px; }
        .filters select { padding: 6px 10px; border: 1px solid #dfe6e9; border-radius: 6px; font-size: 13px; min-width: 150px; }
        .filters button { background: #14b8a6; color: #fff; border: none; padding: 6px 16px; border-radius: 6px; font-size: 13px; }
        .filters a { padding: 6px 12px; font-size: 13px; color: #636e72; text-decoration: none; }
        .perf-table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 13px; }
        .perf-table th { background: #f8f9fc; color: #595d64; font-size: 12px; padding: 10px; text-align: right; white-space: nowrap; }
        .perf-table td { padding: 10px; border-bottom: 1px solid #f0f0f0; vertical-align: middle; }
        .product-cell { display: flex; align-items: center; gap: 10px; }
        .product-cell img { width: 45px; height: 45px; object-fit: cover; border-radius: 8px; background: #f1f2f6; }
        .product-name { font-weight: 700; color: #1f2937; }
        .product-cat { font-size: 11px; color: #95a5a6; }
        .score-bar { width: 90px; }
        .recommendation { font-size: 12px; color: #636e72; max-width: 280px; line-height: 1.6; }

        /* الآيباد */
        @media (max-width: 992px) {
            .perf-grid-2, .perf-grid-3 { grid-template-columns: 1fr; }
        }

        /* الجوال */
        @media (max-width: 768px) {
            .perf-grid-2, .perf-grid-3 { display: flex; flex-direction: column; gap: 20px; }
            .perf-table thead { display: none; }
            .perf-table tr { display: block; border: 1px solid #f0f0f0; border-radius: 10px; margin-bottom: 12px; padding: 8px; }
            .perf-table td { display: flex; justify-content: space-between; align-items: center; gap: 10px; border: none; padding: 6px 4px; }
            .perf-table td::before { content: attr(data-label); font-size: 11px; font-weight: 700; color: #95a5a6; white-space: nowrap; }
            .perf-table td.product-td::before { display: none; }
            .recommendation { max-width: 100%; text-align: left; }
            .filters select, .filters button { flex: 1; }
        }
    </style>
@endsection

@section('content')
    @php
        $funnel = $charts['funnel'];
        $checkout = $funnel['cart_adds'] ? round($funnel['sales'] / $funnel['cart_adds'] * 100, 1) : 0;
        $short = fn ($n) => $n >= 1000 ? round($n / 1000, 1) . 'k' : $n;
    @endphp

    <div class="wrapper" dir="rtl">

        {{-- الرأس --}}
        <div class="top-header">
            <div class="header-top">
                <div class="left-title">
                    <div class="section-title" style="margin-bottom: 0px;">
                        <h3 style="font-size: 20px">أداء <span class="orange-text">المنتجات</span></h3>
                    </div>
                    <p>تحليل ذكي لأداء منتجاتك مع التوصيات</p>
                </div>

                <div class="right-info">
                    <div class="info-box">
                        <span class="label">تاريخ التحليل</span>
                        <span class="value">{{ $analysisDate }}</span>
                    </div>
                    <div class="divider"></div>
                    <div class="info-box">
                        <span class="label">آخر تحديث</span>
                        <span class="value">{{ $lastUpdate ? \Carbon\Carbon::parse($lastUpdate)->timezone('Asia/Aden')->format('h:i A') : '-' }}</span>
                    </div>
                    <div class="divider"></div>
                    <form method="POST" action="{{ route('product-performance.refresh') }}" onsubmit="this.querySelector('button').disabled = true;">
                        @csrf
                        <button type="submit" class="export-btn" title="تحديث التحليل"><i class="bi bi-arrow-clockwise"></i></button>
                    </form>
                </div>
            </div>

            <div class="header-bottom">
                <span class="method-badge">
                    <i class="bi bi-cpu"></i>
                    {{ $usedKMeans ? 'التصنيف بالذكاء الاصطناعي (K-Means)' : 'التصنيف حسب نسبة الأداء' }}
                </span>
                <div class="actions">
                    <a href="#ai-report">التقرير الذكي</a>
                    <a href="#products-table">جدول المنتجات</a>
                </div>
            </div>
        </div>

        {{-- الصف الأول --}}
        <div class="grid-layout">

            {{-- المخطط الكبير --}}
            <div class="panel">
                <div class="heading">أداء المنتجات</div>
                <div class="meta-row">
                    @foreach (['high', 'normal', 'stagnant', 'pending'] as $key)
                        <div>
                            <div class="meta-title">{{ $statuses[$key]['label'] }}</div>
                            <div class="meta-value" style="color: {{ $statuses[$key]['color'] }};">{{ $cards[$key] }}</div>
                        </div>
                    @endforeach
                </div>
                <div class="chart-lg"><canvas id="mainChart"></canvas></div>
            </div>

            {{-- المؤشرات المصغرة --}}
            <div class="panel panel-split">
                <div class="half" style="display: flex; gap: 15px;">
                    <div style="flex: 1; direction: rtl;">
                        <div class="metric-top">
                            <div class="metric-number">{{ $short($funnel['views']) }}</div>
                            <div class="chip">{{ $cards['conversion'] }}% تحويل</div>
                        </div>
                        <div class="description">اجمالي المشاهدات</div>
                        <div class="chart-sm"><canvas id="viewsChart"></canvas></div>
                    </div>
                    <div style="flex: 1; direction: rtl;">
                        <div class="metric-top">
                            <div class="metric-number">{{ $short($funnel['sales']) }}</div>
                            <div class="chip purple">{{ $cards['high_ratio'] }}% عالي</div>
                        </div>
                        <div class="description">اجمالي المبيعات</div>
                        <div class="chart-sm"><canvas id="salesChart"></canvas></div>
                    </div>
                </div>

                <div class="half">
                    <div style="flex: 1;">
                        <div class="heading">السلة & المبيعات</div>
                        <div class="metric-top" style="margin-top: 10px;">
                            <div class="metric-number" style="font-size: 32px;">{{ $short($funnel['cart_adds']) }}</div>
                            <div class="metric-change green">↑ {{ $checkout }}%</div>
                        </div>
                        <div class="description">مرات الإضافة للسلة، ونسبة ما تحول منها إلى شراء.</div>
                    </div>
                    <div style="flex: 1; height: 140px; padding: 10px;"><canvas id="cartSalesChart"></canvas></div>
                </div>
            </div>
        </div>

        {{-- الصف الثاني --}}
        <div class="bottom-grid">

            <div class="panel" dir="rtl">
                <div class="heading">إحصائيات</div>
                <div class="description">الأكثر طلباً من طلبات العملاء.</div>
                <div style="display: flex; justify-content: space-around; gap: 30px;">
                    <div class="acq-row">
                        <div class="acq-box" style="background-color: #14b8a6;"><i class="bi bi-rulers" style="color: #fff; font-size: 24px;"></i></div>
                        <div>
                            <div class="meta-title">المقاس</div>
                            <div class="meta-value">{{ $cards['top_size'] }}</div>
                        </div>
                    </div>
                    <div class="acq-row">
                        <div class="acq-box purple"><i class="bi bi-palette" style="color: #fff; font-size: 24px;"></i></div>
                        <div>
                            <div class="meta-title">اللون</div>
                            <div class="meta-value"><span class="color-dot" style="background: {{ $cards['top_color_code'] }};"></span>{{ $cards['top_color'] }}</div>
                        </div>
                    </div>
                </div>

                <hr style="margin: 25px 0; border: none; border-top: 1px solid #eee;">

                <div class="heading">الاداء</div>
                <div class="description">مؤشرات أداء المتجر</div>
                <div class="session-circles">
                    <div class="circle-box">
                        <canvas id="highCircle"></canvas>
                        <div>
                            <div class="meta-title">منتجات عالية</div>
                            <div class="meta-value">{{ $cards['high_ratio'] }}%</div>
                        </div>
                    </div>
                    <div class="circle-box">
                        <canvas id="conversionCircle"></canvas>
                        <div>
                            <div class="meta-title">نسبة التحويل</div>
                            <div class="meta-value">{{ $cards['conversion'] }}%</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel" dir="rtl">
                <div class="heading">حالة المنتجات</div>
                <div class="channel-grid">
                    <div style="display: flex; flex-direction: column; justify-content: space-between;">
                        @foreach ($statuses as $key => $status)
                            @php $percent = $cards['total'] ? round($cards[$key] / $cards['total'] * 100) : 0; @endphp
                            <div>
                                <div class="channel-item">
                                    <div>{{ $status['label'] }}</div>
                                    <div>{{ $cards[$key] }} ({{ $percent }}%)</div>
                                </div>
                                <div class="bar"><span style="width: {{ $percent }}%; background: {{ $status['color'] }};"></span></div>
                            </div>
                        @endforeach
                    </div>
                    <div class="donut-wrap"><canvas id="statusChart"></canvas></div>
                </div>
            </div>
        </div>

        {{-- أفضل وأسوأ 10 --}}
        <div class="perf-grid-2">
            @foreach ([['أفضل 10 منتجات', $topProducts, 'high', false], ['أكثر 10 منتجات ركوداً', $worstProducts, 'stagnant', true]] as [$title, $list, $filter, $isWorst])
                <div class="panel">
                    <div class="panel-head">
                        <div class="heading">{{ $title }}</div>
                        <a class="panel-link" href="{{ route('product-performance', ['status' => $filter]) }}#products-table">عرض الكل</a>
                    </div>
                    <div class="rank-list">
                        @forelse ($list as $i => $product)
                            <div class="rank-item">
                                <div class="rank-num {{ $isWorst ? 'red' : '' }}">{{ $i + 1 }}</div>
                                <img src="{{ asset('storage/uploads/products/' . $product->p_image) }}" alt="" loading="lazy" onerror="this.style.visibility='hidden'">
                                <div class="rank-body">
                                    <div class="rank-name">{{ $product->p_name }}</div>
                                    <div class="rank-sub">
                                        {{ $isWorst ? ($diagnoses[$product->diagnosis]['label'] ?? '') : $product->cat_name . ' · ' . $product->sales . ' مبيعات' }}
                                    </div>
                                    <div class="bar"><span style="width: {{ $product->score }}%; background: {{ $statuses[$product->status]['color'] }};"></span></div>
                                </div>
                                <div class="rank-score">{{ round($product->score) }}%</div>
                            </div>
                        @empty
                            <div class="chart-empty">لا توجد منتجات مقيّمة بعد</div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>

        {{-- المقاسات والألوان ومسار الشراء --}}
        <div class="perf-grid-3">
            <div class="panel">
                <div class="heading">المقاسات الأكثر مبيعاً</div>
                <div class="description">عدد القطع المباعة من كل مقاس</div>
                <div class="chart-md"><canvas id="sizesChart"></canvas></div>
            </div>

            <div class="panel">
                <div class="heading">الألوان الأكثر طلباً</div>
                <div class="description">عدد القطع المباعة من كل لون</div>
                <div class="chart-md" style="height: 170px;"><canvas id="colorsChart"></canvas></div>
                <div style="margin-top: 10px;">
                    @foreach ($charts['colors']->take(4) as $color)
                        <div class="channel-item">
                            <div><span class="color-dot" style="background: {{ $color->code }};"></span>{{ $color->name }}</div>
                            <div>{{ $color->total }} قطعة</div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="panel">
                <div class="heading">مسار الشراء</div>
                <div class="description">من المشاهدة حتى الشراء</div>
                <div style="margin-top: 20px;">
                    @foreach ([['المشاهدات', $funnel['views'], '#0ea5e9'], ['الإضافة للسلة', $funnel['cart_adds'], '#6c5ce7'], ['المبيعات', $funnel['sales'], '#14b8a6']] as [$label, $value, $color])
                        @php $percent = $funnel['views'] ? round($value / $funnel['views'] * 100, 1) : 0; @endphp
                        <div class="channel-item">
                            <div>{{ $label }}</div>
                            <div>{{ number_format($value) }} ({{ $percent }}%)</div>
                        </div>
                        <div class="bar" style="height: 10px; margin-bottom: 18px;"><span style="width: {{ max($percent, 2) }}%; background: {{ $color }};"></span></div>
                    @endforeach
                </div>
                <div class="description" style="margin-top: 5px;">
                    من كل 100 مشاهدة يشتري {{ $funnel['views'] ? round($funnel['sales'] / $funnel['views'] * 100, 1) : 0 }} عميل.
                </div>
            </div>
        </div>

        {{-- خريطة المجموعات وأداء الأقسام --}}
        <div class="grid-layout">
            <div class="panel">
                <div class="heading">خريطة مجموعات المنتجات</div>
                <div class="description">كل نقطة منتج، ولونها المجموعة التي وضعته فيها خوارزمية التصنيف</div>
                <div class="chart-lg"><canvas id="clustersChart"></canvas></div>
            </div>

            <div class="panel">
                <div class="heading">أداء الأقسام</div>
                <div class="description">متوسط نسبة أداء منتجات كل قسم</div>
                <div style="margin-top: 15px;">
                    @forelse ($charts['categories'] as $category)
                        <div class="channel-item">
                            <div>{{ $category['name'] }} <span class="product-cat">({{ $category['count'] }} منتج)</span></div>
                            <div>{{ $category['score'] }}%</div>
                        </div>
                        <div class="bar"><span style="width: {{ $category['score'] }}%; background: {{ ['#14b8a6', '#3b82f6', '#6c5ce7', '#0ea5e9', '#9b92aa'][$loop->index % 5] }};"></span></div>
                    @empty
                        <div class="chart-empty">لا توجد منتجات مقيّمة بعد</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- جدول المنتجات --}}
        <div class="panel perf-full" id="products-table">
            <div class="heading">تفاصيل أداء المنتجات</div>

            <form method="GET" action="{{ route('product-performance') }}#products-table" class="filters">
                <select name="status">
                    <option value="">كل الحالات</option>
                    @foreach ($statuses as $key => $status)
                        <option value="{{ $key }}" @selected(request('status') === $key)>{{ $status['label'] }}</option>
                    @endforeach
                </select>
                <select name="category">
                    <option value="">كل الأقسام</option>
                    @foreach ($categories as $id => $name)
                        <option value="{{ $id }}" @selected((string) request('category') === (string) $id)>{{ $name }}</option>
                    @endforeach
                </select>
                <button type="submit">تطبيق</button>
                @if (request('status') || request('category'))
                    <a href="{{ route('product-performance') }}#products-table">إلغاء الفلترة</a>
                @endif
            </form>

            <div style="overflow-x: auto;">
                <table class="perf-table">
                    <thead>
                        <tr>
                            <th>المنتج</th>
                            <th>الحالة</th>
                            <th>نسبة الأداء</th>
                            <th>المشاهدات</th>
                            <th>السلة</th>
                            <th>المبيعات</th>
                            <th>السبب والتوصية</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            @php
                                $status = $statuses[$product->status];
                                $diagnosis = $diagnoses[$product->diagnosis] ?? null;
                            @endphp
                            <tr>
                                <td class="product-td">
                                    <div class="product-cell">
                                        <img src="{{ asset('storage/uploads/products/' . $product->p_image) }}" alt="" loading="lazy" onerror="this.style.visibility='hidden'">
                                        <div>
                                            <div class="product-name">{{ $product->p_name }}</div>
                                            <div class="product-cat">{{ $product->cat_name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td data-label="الحالة">
                                    <span class="status-badge" style="background: {{ $status['color'] }};">{{ $status['label'] }}</span>
                                </td>
                                <td data-label="نسبة الأداء">
                                    @if ($product->status === 'pending')
                                        -
                                    @else
                                        <div class="score-bar">
                                            <div style="font-weight: 700;">{{ round($product->score) }}%</div>
                                            <div class="bar" style="margin: 4px 0 0;"><span style="width: {{ $product->score }}%; background: {{ $status['color'] }};"></span></div>
                                        </div>
                                    @endif
                                </td>
                                <td data-label="المشاهدات">{{ $product->views }}</td>
                                <td data-label="السلة">{{ $product->cart_adds }}</td>
                                <td data-label="المبيعات">{{ $product->sales }}</td>
                                <td data-label="السبب">
                                    @if ($diagnosis)
                                        <div>
                                            <div style="font-weight: 700;">{{ $diagnosis['label'] }}</div>
                                            <div class="recommendation">{{ $diagnosis['recommendation'] }}</div>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="description" style="text-align: center;">لا توجد منتجات بهذه الفلترة</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 15px;">
                {{ $products->fragment('products-table')->links('pagination::bootstrap-5') }}
            </div>
        </div>
        {{-- التقرير الذكي --}}
        <div class="panel perf-full" id="ai-report">
            <div class="panel-head">
                <div>
                    <div class="heading">التقرير الذكي</div>
                    <div class="description" style="margin-top: 5px;">تقرير وتوصيات يكتبها الذكاء الاصطناعي من نتائج التحليل</div>
                </div>
                <button type="button" class="ai-btn" id="aiReportBtn">
                    <i class="bi bi-stars"></i> {{ $aiReport ? 'إعادة إنشاء التقرير' : 'إنشاء التقرير' }}
                </button>
            </div>
            <div class="ai-report" id="aiReportBox">
                @if ($aiReport)
                    {!! $aiReport !!}
                @else
                    <div class="description">اضغط على "إنشاء التقرير" ليكتب لك الذكاء الاصطناعي ملخصاً وخطة عمل.</div>
                @endif
            </div>
        </div>

    </div>
@endsection

@section('jsfile')
    <script>
        const charts = @json($charts);
        const statuses = @json($statuses);
        const cards = @json($cards);
        const topProducts = @json($topProducts);

        // إعدادات الرئيسية: بدون محاور ولا وسيلة إيضاح في المخططات الصغيرة
        Chart.defaults.font.family = 'Arial, Helvetica, sans-serif';
        Chart.defaults.plugins.tooltip.rtl = true;
        const hidden = { plugins: { legend: { display: false } }, scales: { x: { display: false }, y: { display: false } }, responsive: true, maintainAspectRatio: false };

        // رسالة بدل المخطط عند عدم وجود بيانات
        function showEmpty(id, message) {
            document.getElementById(id).parentElement.innerHTML = `<div class="chart-empty">${message}</div>`;
        }

        // ترتيب المنتجات المقيّمة حسب نسبة الأداء
        const rated = [...charts.clusters].sort((a, b) => b.y - a.y);

        // 1. المخطط الكبير: تطور المتجر بعد يومين، وقبلها منحنى أداء المنتجات
        if (charts.trend.length >= 2) {
            new Chart(mainChart, {
                type: 'line',
                data: {
                    labels: charts.trend.map(d => d.date.slice(5)),
                    datasets: [
                        { label: 'منتجات عالية الأداء', data: charts.trend.map(d => d.high), borderColor: '#14b8a6', backgroundColor: 'rgba(20,184,166,0.15)', fill: true, tension: 0.4, pointRadius: 0 },
                        { label: 'منتجات راكدة', data: charts.trend.map(d => d.stagnant), borderColor: '#0984e3', backgroundColor: 'rgba(9,132,227,0.15)', fill: true, tension: 0.4, pointRadius: 0 }
                    ]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { title: { display: true, text: 'الأيام', font: { size: 14, weight: 'bold' } } }, y: { beginAtZero: true } } }
            });
        } else if (rated.length) {
            new Chart(mainChart, {
                type: 'line',
                data: {
                    labels: rated.map(p => p.name),
                    datasets: [
                        { label: 'نسبة الأداء', data: rated.map(p => p.y), borderColor: '#14b8a6', backgroundColor: 'rgba(20,184,166,0.15)', fill: true, tension: 0.4, pointRadius: 0 },
                        { label: 'نسبة التحويل', data: rated.map(p => p.x ? Math.round(p.sales / p.x * 1000) / 10 : 0), borderColor: '#0984e3', backgroundColor: 'rgba(9,132,227,0.15)', fill: true, tension: 0.4, pointRadius: 0, yAxisID: 'y1' }
                    ]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => ctx.dataset.label + ': ' + ctx.raw + '%' } } },
                    scales: { x: { ticks: { display: false }, title: { display: true, text: 'المنتجات', font: { size: 14, weight: 'bold' } } }, y: { beginAtZero: true, max: 100 }, y1: { display: false, beginAtZero: true } }
                }
            });
        } else {
            showEmpty('mainChart', 'لا توجد منتجات مقيّمة بعد');
        }

        // 2. مشاهدات المنتجات (أعمدة صغيرة)
        const byViews = [...charts.clusters].sort((a, b) => b.x - a.x);
        byViews.length
            ? new Chart(viewsChart, { type: 'bar', data: { labels: byViews.map(p => p.name), datasets: [{ data: byViews.map(p => p.x), backgroundColor: '#2f80ed', borderRadius: 4 }] }, options: hidden })
            : showEmpty('viewsChart', '-');

        // 3. مبيعات المنتجات (منحنى صغير)
        rated.length
            ? new Chart(salesChart, { type: 'line', data: { labels: rated.map(p => p.name), datasets: [{ data: rated.map(p => p.sales), borderColor: '#14b8a6', backgroundColor: 'rgba(20,184,166,0.15)', fill: true, tension: 0.4, pointRadius: 0 }] }, options: hidden })
            : showEmpty('salesChart', '-');

        // 4. السلة مقابل المبيعات لأفضل 8 منتجات (أعمدة بلونين)
        const top8 = topProducts.slice(0, 8);
        top8.length
            ? new Chart(cartSalesChart, {
                type: 'bar',
                data: {
                    labels: top8.map(p => p.p_name),
                    datasets: [
                        { label: 'المبيعات', data: top8.map(p => p.sales), backgroundColor: '#6c5ce7' },
                        { label: 'الإضافة للسلة', data: top8.map(p => p.cart_adds), backgroundColor: '#9b92aa' }
                    ]
                },
                options: hidden
            })
            : showEmpty('cartSalesChart', '-');

        // 5. دوائر الأداء
        const circle = (id, value, color) => new Chart(document.getElementById(id), {
            type: 'doughnut',
            data: { datasets: [{ data: [value, Math.max(0, 100 - value)], backgroundColor: [color, '#e5e7eb'], borderWidth: 0 }] },
            options: { plugins: { legend: { display: false }, tooltip: { enabled: false } }, cutout: '70%', responsive: true, maintainAspectRatio: true }
        });
        circle('highCircle', cards.high_ratio, '#3b82f6');
        circle('conversionCircle', cards.conversion, '#14b8a6');

        // 6. توزيع الحالات
        new Chart(statusChart, {
            type: 'doughnut',
            data: {
                labels: Object.keys(statuses).map(k => statuses[k].label),
                datasets: [{ data: Object.keys(statuses).map(k => charts.statuses[k] || 0), backgroundColor: Object.keys(statuses).map(k => statuses[k].color), borderWidth: 0 }]
            },
            options: { plugins: { legend: { display: false } }, cutout: '65%', responsive: true, maintainAspectRatio: false }
        });

        // 7. المقاسات
        charts.sizes.length
            ? new Chart(sizesChart, {
                type: 'bar',
                data: { labels: charts.sizes.map(s => s.name), datasets: [{ data: charts.sizes.map(s => s.total), backgroundColor: ['#6c5ce7', '#9b92aa'], borderRadius: 6, barThickness: 22 }] },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => ctx.raw + ' قطعة' } } },
                    scales: { x: { grid: { display: false }, border: { display: false } }, y: { display: false, beginAtZero: true } }
                }
            })
            : showEmpty('sizesChart', 'لا توجد طلبات بمقاسات بعد');

        // 8. الألوان بألوانها الحقيقية
        charts.colors.length
            ? new Chart(colorsChart, {
                type: 'doughnut',
                data: { labels: charts.colors.map(c => c.name), datasets: [{ data: charts.colors.map(c => c.total), backgroundColor: charts.colors.map(c => c.code || '#e5e7eb'), borderColor: '#fff', borderWidth: 2 }] },
                options: { plugins: { legend: { display: false } }, cutout: '65%', responsive: true, maintainAspectRatio: false }
            })
            : showEmpty('colorsChart', 'لا توجد طلبات بألوان بعد');

        // 9. خريطة المجموعات
        charts.clusters.length
            ? new Chart(clustersChart, {
                type: 'scatter',
                data: {
                    datasets: ['high', 'normal', 'stagnant'].map(key => ({
                        label: statuses[key].label,
                        data: charts.clusters.filter(p => p.status === key),
                        backgroundColor: statuses[key].color + 'cc',
                        borderColor: '#fff',
                        borderWidth: 2,
                        pointRadius: 8,
                        pointHoverRadius: 10
                    }))
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', rtl: true, labels: { usePointStyle: true, boxWidth: 8 } },
                        tooltip: { callbacks: { label: ctx => ctx.raw.name + ': ' + ctx.raw.y + '% (' + ctx.raw.x + ' مشاهدة)' } }
                    },
                    scales: {
                        x: { beginAtZero: true, title: { display: true, text: 'المشاهدات', font: { size: 14, weight: 'bold' } } },
                        y: { min: 0, max: 100, title: { display: true, text: 'نسبة الأداء', font: { size: 14, weight: 'bold' } } }
                    }
                }
            })
            : showEmpty('clustersChart', 'لا توجد منتجات مقيّمة بعد');

        // التقرير الذكي
        document.getElementById('aiReportBtn').addEventListener('click', async function () {
            const button = this;
            const box = document.getElementById('aiReportBox');
            const original = button.innerHTML;

            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm"></span> جاري كتابة التقرير...';

            try {
                const response = await fetch('{{ route('product-performance.ai-report') }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                });
                const result = await response.json();

                if (result.success) {
                    box.innerHTML = result.report;
                } else {
                    Swal.fire({ icon: 'error', title: 'خطأ', text: result.message });
                }
            } catch (e) {
                Swal.fire({ icon: 'error', title: 'خطأ', text: 'تعذر الاتصال بالخادم' });
            }

            button.disabled = false;
            button.innerHTML = original;
        });
    </script>
@endsection