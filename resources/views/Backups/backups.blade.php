@extends('Layouts.master')

@section('link')
    <link href="{{ secure_asset('assets/css/order.css') }}?v={{ time() }}" rel="stylesheet">
    <link href="{{ asset('assets/css/order.css') }}?v={{ time() }}" rel="stylesheet">
    <link href="{{ secure_asset('assets/css/login.css') }}?v={{ time() }}" rel="stylesheet">
    <link href="{{ asset('assets/css/login.css') }}?v={{ time() }}" rel="stylesheet">
@endsection


@section('content')
<div class="container my-4 px-2 px-md-2 at-page" dir="rtl">
    <div class="container">

        {{-- العنوان والأزرار --}}
        <div class="controls-container at-keep-title">
            <div class="section-ar">
                <div class="section-title" style="margin-bottom: 0px;">
                    <h3 style="font-size: 20px">النسخ <span class="orange-text">{{ $meta['title'] }}</span></h3>
                </div>
            </div>

            <div class="ms-auto d-flex gap-2">
                <a href="{{ url('backups/' . $type) }}" class="at-btn" title="تحديث الصفحة">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>

                <a href="{{ url('addbackups') }}" class="at-btn-solid" title="إنشاء نسخة جديدة">
                    <i class="bi bi-plus-lg"></i>
                    <span class="at-btn-text">نسخة جديدة</span>
                </a>
            </div>
        </div>

        {{-- التنقل بين الأنواع --}}
        <div class="at-tabs">
            @foreach (['daily' => 'اليومية', 'weekly' => 'الأسبوعية', 'monthly' => 'الشهرية', 'yearly' => 'السنوية'] as $key => $label)
                <a href="{{ url('backups/' . $key) }}" class="{{ $type === $key ? 'active' : '' }}">{{ $label }}</a>
            @endforeach
        </div>

        {{-- الإحصائيات --}}
        <div class="at-stats">
            <div class="at-stat">
                <div class="icon"><i class="bi {{ $meta['icon'] }}"></i></div>
                <div>
                    <span class="value">{{ $stats['count'] }}</span>
                    <span class="label">عدد النسخ {{ $meta['title'] }}</span>
                </div>
            </div>
            <div class="at-stat">
                <div class="icon"><i class="bi bi-hourglass-split"></i></div>
                <div>
                    <span class="value">{{ $stats['keep'] }} {{ $meta['unit'] }}</span>
                    <span class="label">مدة الاحتفاظ</span>
                </div>
            </div>
            <div class="at-stat">
                <div class="icon"><i class="bi bi-clock-history"></i></div>
                <div>
                    <span class="value">{{ $stats['latest'] ? $stats['latest']->format('Y-m-d') : '—' }}</span>
                    <span class="label">آخر نسخة</span>
                </div>
            </div>
            <div class="at-stat">
                <div class="icon"><i class="bi bi-hdd"></i></div>
                <div>
                    <span class="value">{{ $stats['size'] }}</span>
                    <span class="label">الحجم الكلي</span>
                </div>
            </div>
        </div>

        {{-- جدول النسخ --}}
        <div class="table-container table-responsive">
            @if ($backups->isEmpty())
                <div class="at-empty">
                    <i class="bi bi-archive"></i>
                    لا توجد نسخ {{ $meta['title'] }} حتى الآن
                    <br>
                    <a href="{{ url('addbackups') }}" class="at-btn-solid">
                        <i class="bi bi-plus-lg"></i> إنشاء نسخة الآن
                    </a>
                </div>
            @else
                <table class="table align-middle at-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>النسخة</th>
                            <th>النوع</th>
                            <th>الوقت</th>
                            <th>الحجم</th>
                            <th class="text-center">العمليات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($backups as $backup)
                        <tr class="card-item">
                            <td><span class="at-num">{{ $loop->iteration }}</span></td>
                            <td class="fw-bold">
                                <span class="at-file">
                                    <i class="bi bi-file-earmark-lock"></i>
                                    <span class="card-title">{{ $backup['date']->format('Y-m-d') }}</span>
                                </span>
                            </td>
                            <td>
                                <div class="d-inline-flex flex-wrap gap-1 justify-content-center">
                                    @if (str_starts_with($backup['file'], 'pre-restore-'))
                                        <span class="at-pill navy">قبل الاسترجاع</span>
                                    @elseif ($backup['manual'])
                                        <span class="at-pill amber">يدوية</span>
                                    @else
                                        <span class="at-pill green">تلقائية</span>
                                    @endif
                                    @if ($loop->first)
                                        <span class="at-pill solid">الأحدث</span>
                                    @endif
                                </div>
                            </td>
                            <td class="at-muted at-nowrap">{{ $backup['date']->format('h:i A') }}</td>
                            <td class="at-muted at-nowrap" dir="ltr">{{ $backup['size'] }}</td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button class="at-actions-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="العمليات">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>

                                    <ul class="dropdown-menu dropdown-menu-end shadow border-light at-menu">
                                        <li>
                                            <a class="dropdown-item text-end" href="{{ url('/backup-download/' . $backup['file']) }}">
                                                <i class="bi bi-download text-success"></i>
                                                <span>تحميل النسخة</span>
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item text-danger text-end" href="{{ url('/restore-backup/' . $backup['file']) }}">
                                                <i class="bi bi-arrow-counterclockwise"></i>
                                                <span>استرجاع النسخة</span>
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
