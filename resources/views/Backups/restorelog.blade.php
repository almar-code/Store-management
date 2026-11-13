@extends('Layouts.master')

@section('link')
    <link href="{{ secure_asset('assets/css/order.css') }}?v={{ time() }}" rel="stylesheet">
    <link href="{{ asset('assets/css/order.css') }}?v={{ time() }}" rel="stylesheet">
    <link href="{{ secure_asset('assets/css/login.css') }}?v={{ time() }}" rel="stylesheet">
    <link href="{{ asset('assets/css/login.css') }}?v={{ time() }}" rel="stylesheet">

    <style>
        /* ===== الإحصائيات ===== */
        .bk-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            margin: 6px 0 22px;
        }

        .bk-stat {
            display: flex;
            align-items: center;
            gap: 12px;
            background: #fff;
            border: 1px solid #e9f1ef;
            border-radius: 14px;
            padding: 14px 16px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.04);
        }

        .bk-stat .icon {
            width: 44px;
            height: 44px;
            flex-shrink: 0;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .bk-stat .value { display: block; font-size: 17px; font-weight: 700; color: #000; line-height: 1.3; }
        .bk-stat .label { font-size: 12.5px; color: #6c757d; }

        /* ===== الجدول ===== */
        .rs-file {
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .rs-file i {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: #f1f8f6;
            color: #008870;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
        }

        .rs-file small { display: block; color: #6c757d; font-weight: 400; font-size: 12px; margin-top: 4px; }

        .rs-status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 50px;
            font-size: 12.5px;
            font-weight: 700;
            white-space: nowrap;
        }

        /* ===== حالة عدم وجود عمليات ===== */
        .bk-empty { text-align: center; padding: 45px 10px; color: #6c757d; }
        .bk-empty i { font-size: 46px; color: #cfe6e1; display: block; margin-bottom: 10px; }

        @media (max-width: 768px) {
            .bk-stats { grid-template-columns: 1fr 1fr; }
        }

        /* إظهار عنوان الصفحة في الجوال */
        @media (max-width: 1199px) {
            .section-ar { display: flex !important; }
        }
    </style>
@endsection

@section('content')
<div class="container my-4 px-2 px-md-2" dir="rtl">
    <div class="container">

        {{-- العنوان والأزرار --}}
        <div class="controls-container">
            <div class="section-ar">
                <div class="section-title" style="margin-bottom: 0px;">
                    <h3 style="font-size: 20px">سجل <span class="orange-text">الاسترجاع</span></h3>
                </div>
            </div>

            <a href="{{ url('backup-restores') }}" class="btn-add-product shadow-sm" style="justify-content: center;" title="تحديث الصفحة">
                <i class="bi bi-arrow-clockwise"></i>
            </a>

            <a href="{{ url('backups/daily') }}" class="btn-add-product shadow-sm ms-2" style="justify-content: center; background-color: #008870; color: #fff;" title="اختيار نسخة للاسترجاع">
                <i class="bi bi-archive fs-5"></i>
            </a>
        </div>

        {{-- الإحصائيات --}}
        <div class="bk-stats">
            <div class="bk-stat">
                <div class="icon" style="background:#e6f5f2; color:#008870;"><i class="bi bi-list-check"></i></div>
                <div>
                    <span class="value">{{ $stats['total'] }}</span>
                    <span class="label">كل العمليات</span>
                </div>
            </div>
            <div class="bk-stat">
                <div class="icon" style="background:#e6f5f2; color:#008870;"><i class="bi bi-check-circle"></i></div>
                <div>
                    <span class="value">{{ $stats['success'] }}</span>
                    <span class="label">ناجحة</span>
                </div>
            </div>
            <div class="bk-stat">
                <div class="icon" style="background:#e8eef6; color:#1a4372;"><i class="bi bi-arrow-counterclockwise"></i></div>
                <div>
                    <span class="value">{{ $stats['undone'] }}</span>
                    <span class="label">تم التراجع عنها</span>
                </div>
            </div>
            <div class="bk-stat">
                <div class="icon" style="background:#fdecec; color:#d63031;"><i class="bi bi-x-circle"></i></div>
                <div>
                    <span class="value">{{ $stats['failed'] }}</span>
                    <span class="label">فاشلة</span>
                </div>
            </div>
        </div>

        {{-- الجدول --}}
        <div class="table-container">
            @if ($restores->isEmpty())
                <div class="bk-empty">
                    <i class="bi bi-clock-history"></i>
                    لا توجد عمليات استرجاع حتى الآن
                </div>
            @else
                <table class="table">
                    <thead>
                        <tr>
                            <th>رقم</th>
                            <th>النسخة المسترجعة</th>
                            <th>بواسطة</th>
                            <th>التاريخ</th>
                            <th>الحالة</th>
                            <th>العمليات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($restores as $restore)
                        @php
                            $info = $restore->statusInfo();
                            $canUndo = $restore->status === 'success' && $restore->safety_file && isset($files[$restore->safety_file]);
                        @endphp
                        <tr class="card-item">
                            <td class="fs-6 fs-md-5">#{{ $restore->id }}</td>
                            <td class="card-title fw-bold lh-1 fs-md-5">
                                <span class="rs-file">
                                    <i class="bi {{ $restore->undo_of ? 'bi-arrow-counterclockwise' : 'bi-file-earmark-lock' }}"></i>
                                    <span>
                                        {{ $restore->undo_of ? 'تراجع عن العملية #' . $restore->undo_of : $restore->backup_file }}
                                        <small>{{ Str::limit($restore->message, 60) }}</small>
                                    </span>
                                </span>
                            </td>
                            <td class="text-muted lh-1 fs-md-5">{{ $restore->userName() }}</td>
                            <td class="text-muted lh-1 fs-md-5">{{ $restore->created_at->format('Y-m-d h:i A') }}</td>
                            <td>
                                <span class="rs-status" style="background: {{ $info['bg'] }}; color: {{ $info['color'] }};">
                                    <i class="bi {{ $info['icon'] }}"></i> {{ $info['label'] }}
                                </span>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn text-dark p-0 border-0 shadow-none" type="button"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-three-dots-vertical fs-4"></i>
                                    </button>

                                    <ul class="dropdown-menu dropdown-menu-end shadow border-light">
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center justify-content-between py-2 text-end"
                                                href="{{ url('backup-restores/' . $restore->id) }}">
                                                <span class="ms-2">عرض التفاصيل</span>
                                                <i class="bi bi-eye text-success"></i>
                                            </a>
                                        </li>
                                        @if ($canUndo)
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center justify-content-between py-2 text-end"
                                                    href="{{ url('/restore-backup/' . $restore->safety_file . '?undo=' . $restore->id) }}">
                                                    <span class="ms-2">تراجع عن الاسترجاع</span>
                                                    <i class="bi bi-arrow-counterclockwise text-danger"></i>
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

    </div>
</div>
@endsection
