@extends('Layouts.master')

@section('link')
    <link href="{{ secure_asset('assets/css/order.css') }}?v={{ time() }}" rel="stylesheet">
    <link href="{{ asset('assets/css/order.css') }}?v={{ time() }}" rel="stylesheet">
    <link href="{{ secure_asset('assets/css/login.css') }}?v={{ time() }}" rel="stylesheet">
    <link href="{{ asset('assets/css/login.css') }}?v={{ time() }}" rel="stylesheet">

    <style>
        /* ===== بطاقات الإحصائيات ===== */
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
            background: #e6f5f2;
            color: #008870;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .bk-stat .value {
            display: block;
            font-size: 17px;
            font-weight: 700;
            color: #000;
            line-height: 1.3;
        }

        .bk-stat .label {
            font-size: 12.5px;
            color: #6c757d;
        }

        /* ===== التنقل بين الأنواع ===== */
        .bk-tabs {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 16px;
        }

        .bk-tabs a {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 16px;
            border-radius: 50px;
            border: 1px solid #dfe9e7;
            color: #495057;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.25s ease;
        }

        .bk-tabs a:hover { border-color: #008870; color: #008870; }

        .bk-tabs a.active {
            background: #008870;
            border-color: #008870;
            color: #fff;
            box-shadow: 0 6px 14px rgba(0, 136, 112, 0.25);
        }

        /* ===== أيقونة الملف داخل الجدول ===== */
        .bk-file {
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .bk-file i {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: #f1f8f6;
            color: #008870;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        /* ===== حالة عدم وجود نسخ ===== */
        .bk-empty {
            text-align: center;
            padding: 45px 10px;
            color: #6c757d;
        }

        .bk-empty i {
            font-size: 46px;
            color: #cfe6e1;
            display: block;
            margin-bottom: 10px;
        }

        .bk-empty a {
            display: inline-block;
            margin-top: 12px;
            padding: 8px 22px;
            border-radius: 10px;
            background: #008870;
            color: #fff;
            text-decoration: none;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .bk-stats { grid-template-columns: 1fr 1fr; }
        }
                /* إظهار عنوان الصفحة في الجوال (card.css يخفيه في باقي الصفحات) */
        @media (max-width: 1199px) {
            .section-ar {
                display: flex !important;
                
            }
        }
    </style>
@endsection


@section('content')
<div class="container my-4 px-2 px-md-2" dir="rtl">
    <div class="container">

        {{-- ===== العنوان والأزرار ===== --}}
        <div class="controls-container">
            <div class="section-ar">
                <div class="section-title" style="margin-bottom: 0px;">
                    <h3 style="font-size: 20px">النسخ <span class="orange-text">{{ $meta['title'] }}</span></h3>
                </div>
            </div>

            <a href="{{ url('backups/' . $type) }}" class="btn-add-product shadow-sm" style="justify-content: center;" title="تحديث الصفحة">
                <i class="bi bi-arrow-clockwise"></i>
            </a>

            <a href="{{ url('addbackups') }}" class="btn-add-product shadow-sm ms-2" style="justify-content: center; background-color: #008870; color: #fff;" title="إنشاء نسخة جديدة">
                <i class="bi bi-plus fs-5"></i>
            </a>
        </div>

        {{-- ===== التنقل السريع بين الأنواع ===== --}}
        <div class="bk-tabs">
            @foreach (['daily' => 'اليومية', 'weekly' => 'الأسبوعية', 'monthly' => 'الشهرية', 'yearly' => 'السنوية'] as $key => $label)
                <a href="{{ url('backups/' . $key) }}" class="{{ $type === $key ? 'active' : '' }}">{{ $label }}</a>
            @endforeach
        </div>

        {{-- ===== الإحصائيات ===== --}}
        <div class="bk-stats">
            <div class="bk-stat">
                <div class="icon"><i class="bi {{ $meta['icon'] }}"></i></div>
                <div>
                    <span class="value">{{ $stats['count'] }}</span>
                    <span class="label">عدد النسخ {{ $meta['title'] }}</span>
                </div>
            </div>
            <div class="bk-stat">
                <div class="icon"><i class="bi bi-hourglass-split"></i></div>
                <div>
                    <span class="value">{{ $stats['keep'] }} {{ $meta['unit'] }}</span>
                    <span class="label">مدة الاحتفاظ</span>
                </div>
            </div>
            <div class="bk-stat">
                <div class="icon"><i class="bi bi-clock-history"></i></div>
                <div>
                    <span class="value">{{ $stats['latest'] ? $stats['latest']->format('Y-m-d') : '—' }}</span>
                    <span class="label">آخر نسخة</span>
                </div>
            </div>
            <div class="bk-stat">
                <div class="icon"><i class="bi bi-hdd"></i></div>
                <div>
                    <span class="value">{{ $stats['size'] }}</span>
                    <span class="label">الحجم الكلي</span>
                </div>
            </div>
        </div>

        {{-- ===== جدول النسخ ===== --}}
        <div class="table-container">
            @if ($backups->isEmpty())
                <div class="bk-empty">
                    <i class="bi bi-archive"></i>
                    لا توجد نسخ {{ $meta['title'] }} حتى الآن
                    <br>
                    <a href="{{ url('addbackups') }}">إنشاء نسخة الآن</a>
                </div>
            @else
                <table class="table">
                    <thead>
                        <tr>
                            <th>متسلسل</th>
                            <th>النسخة</th>
                            <th>الوقت</th>
                            <th>الحجم</th>
                            <th>العمليات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($backups as $backup)
                        <tr class="card-item">
                            <td class="fs-6 fs-md-5">{{ $loop->iteration }}</td>
                            <td class="card-title fw-bold lh-1 fs-md-5">
                                <span class="bk-file">
                                    <i class="bi bi-file-earmark-lock"></i>
                                    {{ $backup['date']->format('Y-m-d') }}
                                </span>
                                @if (str_starts_with($backup['file'], 'pre-restore-'))
                                    <span class="badge ms-1" style="background:#e8eef6; color:#1a4372;">قبل الاسترجاع</span>
                                @elseif ($backup['manual'])
                                    <span class="badge ms-1" style="background:#fff3e0; color:#e8a33d;">يدوية</span>
                                @else
                                    <span class="badge ms-1" style="background:#e6f5f2; color:#008870;">تلقائية</span>
                                @endif
                                @if ($loop->first)
                                    <span class="badge bg-success ms-1">الأحدث</span>
                                @endif
                            </td>
                            <td class="text-muted lh-1 fs-md-5">{{ $backup['date']->format('h:i A') }}</td>
                            <td class="text-muted lh-1 fs-md-5">{{ $backup['size'] }}</td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn text-dark p-0 border-0 shadow-none" type="button"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-three-dots-vertical fs-4"></i>
                                    </button>

                                    <ul class="dropdown-menu dropdown-menu-end shadow border-light">
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center justify-content-between py-2 text-end"
                                                href="{{ url('/backup-download/' . $backup['file']) }}">
                                                <span class="ms-2">تحميل النسخة</span>
                                                <i class="bi bi-download text-success"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center justify-content-between py-2 text-end"
                                                href="{{ url('/restore-backup/' . $backup['file']) }}">
                                                <span class="ms-2">استرجاع النسخة</span>
                                                <i class="bi bi-arrow-counterclockwise text-danger"></i>
                                            </a>
                                        </li>
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
