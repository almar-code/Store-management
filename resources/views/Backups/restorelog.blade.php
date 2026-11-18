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
                    <h3 style="font-size: 20px">سجل <span class="orange-text">الاسترجاع</span></h3>
                </div>
            </div>

            <div class="ms-auto d-flex gap-2">
                <a href="{{ url('backup-restores') }}" class="at-btn" title="تحديث الصفحة">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>

                <a href="{{ url('backups/daily') }}" class="at-btn-solid" title="اختيار نسخة للاسترجاع">
                    <i class="bi bi-archive"></i>
                    <span class="at-btn-text">اختيار نسخة</span>
                </a>
            </div>
        </div>

        {{-- الإحصائيات --}}
        <div class="at-stats">
            <div class="at-stat">
                <div class="icon"><i class="bi bi-list-check"></i></div>
                <div>
                    <span class="value">{{ $stats['total'] }}</span>
                    <span class="label">كل العمليات</span>
                </div>
            </div>
            <div class="at-stat">
                <div class="icon"><i class="bi bi-check-circle"></i></div>
                <div>
                    <span class="value">{{ $stats['success'] }}</span>
                    <span class="label">ناجحة</span>
                </div>
            </div>
            <div class="at-stat">
                <div class="icon navy"><i class="bi bi-arrow-counterclockwise"></i></div>
                <div>
                    <span class="value">{{ $stats['undone'] }}</span>
                    <span class="label">تم التراجع عنها</span>
                </div>
            </div>
            <div class="at-stat">
                <div class="icon red"><i class="bi bi-x-circle"></i></div>
                <div>
                    <span class="value">{{ $stats['failed'] }}</span>
                    <span class="label">فاشلة</span>
                </div>
            </div>
        </div>

        {{-- الجدول --}}
        <div class="table-container table-responsive">
            @if ($restores->isEmpty())
                <div class="at-empty">
                    <i class="bi bi-clock-history"></i>
                    لا توجد عمليات استرجاع حتى الآن
                </div>
            @else
                <table class="table align-middle at-table">
                    <thead>
                        <tr>
                            <th>رقم</th>
                            <th>النسخة المسترجعة</th>
                            <th>بواسطة</th>
                            <th>التاريخ</th>
                            <th>الحالة</th>
                            <th class="text-center">العمليات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($restores as $restore)
                        @php
                            $info = $restore->statusInfo();
                            $canUndo = $restore->status === 'success' && $restore->safety_file && isset($files[$restore->safety_file]);
                        @endphp
                        <tr class="card-item">
                            <td><span class="at-num">#{{ $restore->id }}</span></td>
                            <td class="fw-bold">
                                <span class="at-file">
                                    <i class="bi {{ $restore->undo_of ? 'bi-arrow-counterclockwise' : 'bi-file-earmark-lock' }}"></i>
                                    <span>
                                        <span class="card-title">{{ $restore->undo_of ? 'تراجع عن العملية #' . $restore->undo_of : $restore->backup_file }}</span>
                                        <small>{{ Str::limit($restore->message, 60) }}</small>
                                    </span>
                                </span>
                            </td>
                            <td class="at-muted card-text">{{ $restore->userName() }}</td>
                            <td class="at-muted at-nowrap">{{ $restore->created_at->format('Y-m-d h:i A') }}</td>
                            <td>
                                {{-- ألوان الحالة من الموديل --}}
                                <span class="at-pill" style="background: {{ $info['bg'] }}; color: {{ $info['color'] }};">
                                    <i class="bi {{ $info['icon'] }}"></i> {{ $info['label'] }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button class="at-actions-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="العمليات">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>

                                    <ul class="dropdown-menu dropdown-menu-end shadow border-light at-menu">
                                        <li>
                                            <a class="dropdown-item text-end" href="{{ url('backup-restores/' . $restore->id) }}">
                                                <i class="bi bi-eye text-success"></i>
                                                <span>عرض التفاصيل</span>
                                            </a>
                                        </li>
                                        @if ($canUndo)
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <a class="dropdown-item text-danger text-end" href="{{ url('/restore-backup/' . $restore->safety_file . '?undo=' . $restore->id) }}">
                                                    <i class="bi bi-arrow-counterclockwise"></i>
                                                    <span>تراجع عن الاسترجاع</span>
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
