@extends('Layouts.master')

@section('link')
    <link href="{{ asset('assets/css/login.css') }}?v={{ time() }}" rel="stylesheet">
    <link href="{{ secure_asset('assets/css/login.css') }}?v={{ time() }}" rel="stylesheet">
@endsection

@section('content')
    <style>
        .rpt-btn { display: inline-flex; align-items: center; justify-content: center; gap: 6px; border-radius: 10px; padding: 8px 14px; font-weight: 600; font-size: .9rem; border: 1px solid transparent; transition: .2s; text-decoration: none; cursor: pointer; }
        .rpt-btn-primary { background: #00695c; color: #fff; }
        .rpt-btn-primary:hover { background: #004d40; color: #fff; }
        .rpt-btn-outline { background: #fff; color: #00695c; border-color: #00695c; }
        .rpt-btn-outline:hover { background: #f3f8f7; color: #00695c; }
        .rpt-btn-light { background: #f1f3f5; color: #333; }
        .rpt-btn-light:hover { background: #e2e6ea; color: #333; }
        .rpt-btn-danger { background: #fff; color: #c62828; border-color: #f3c7c7; }
        .rpt-btn-danger:hover { background: #fdecea; color: #c62828; }

        .rpt-item { background: #fff; border-radius: 16px; box-shadow: 0 4px 20px rgba(0, 0, 0, .06); padding: 22px; height: 100%; display: flex; flex-direction: column; transition: .2s; text-align: right; }
        .rpt-item:hover { transform: translateY(-3px); box-shadow: 0 8px 28px rgba(0, 0, 0, .09); }
        .rpt-item-icon { width: 46px; height: 46px; border-radius: 12px; background: #e0f2f1; color: #00695c; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; margin-bottom: 14px; }
        .rpt-item-title { font-size: 1.05rem; font-weight: 700; color: #222; margin-bottom: 8px; }
        .rpt-item-query { color: #666; font-size: .88rem; flex-grow: 1; margin-bottom: 12px; }
        .rpt-item-date { color: #999; font-size: .8rem; margin-bottom: 14px; }
        .rpt-item-actions { display: flex; flex-wrap: wrap; gap: 8px; }
        .rpt-item-actions .rpt-btn { flex: 1 1 auto; }

        .rpt-empty { background: #fff; border-radius: 16px; box-shadow: 0 4px 20px rgba(0, 0, 0, .06); padding: 60px 20px; text-align: center; }
        .rpt-empty i { font-size: 3rem; color: #b2dfdb; }
    </style>

    <div class="container my-4" dir="rtl">
        <div class="container">

            <div class="controls-container">
                <div class="section-ar">
                    <div class="section-title" style="margin-bottom: 0px;">
                        <h3 style="font-size: 20px">قائمة <span class="orange-text">التقارير المحفوظة</span></h3>
                    </div>
                </div>

                <div class="search-input-container">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" class="search-input" placeholder="Search" id="sectionSearch">
                </div>

                <a href="{{ route('reports.custom') }}" class="btn-add-product shadow-sm">
                    تقرير جديد
                    <i class="bi bi-plus-lg ms-2"></i>
                </a>
            </div>
        </div>

        @if ($reports->isEmpty())
            <div class="rpt-empty mt-4 mx-2">
                <i class="bi bi-folder2-open"></i>
                <h5 class="mt-3">لا توجد تقارير محفوظة بعد</h5>
                <p class="text-muted">أنشئ تقريراً واضغط "حفظ التقرير" ليظهر هنا</p>
                <a href="{{ route('reports.custom') }}" class="rpt-btn rpt-btn-primary mt-2">
                    <i class="bi bi-pencil-square"></i> إنشاء تقرير
                </a>
            </div>
        @else
            <div class="row g-3 px-2 mt-1">
                @foreach ($reports as $report)
                    <div class="col-12 col-md-6 col-xl-4 card-item">
                        <div class="rpt-item">
                            <div class="rpt-item-icon"><i class="bi bi-file-earmark-pdf"></i></div>
                            <div class="rpt-item-title card-title">{{ $report->title }}</div>
                            <div class="rpt-item-query card-text">{{ \Illuminate\Support\Str::limit($report->report_query, 110) }}</div>
                            <div class="rpt-item-date">
                                <i class="bi bi-calendar3"></i>
                                {{ $report->created_at->timezone('Asia/Aden')->format('Y/m/d - H:i') }}
                            </div>
                            <div class="rpt-item-actions">
                                <a href="{{ $report->pdf_url }}" target="_blank" class="rpt-btn rpt-btn-outline">
                                    <i class="bi bi-eye"></i> عرض
                                </a>
                                <a href="{{ $report->pdf_url }}" download class="rpt-btn rpt-btn-light">
                                    <i class="bi bi-download"></i> تحميل
                                </a>
                                <a href="#" class="rpt-btn rpt-btn-danger"
                                    onclick="confirmDelete('{{ route('reports.delete', $report->report_id) }}', 'حذف التقرير', 'هل أنت متأكد من حذف هذا التقرير؟'); return false;">
                                    <i class="bi bi-trash"></i> حذف
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4 d-flex justify-content-center">
                {{ $reports->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
@endsection