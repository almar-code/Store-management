@extends('Layouts.master')

@section('link')
    {{-- تحديث تلقائي أثناء التنفيذ --}}
    @if ($restore->isActive())
        <meta http-equiv="refresh" content="3">
    @endif

    <style>
        .rs-result { text-align: center; padding: 6px 0 20px; }

        .rs-result .badge-icon {
            width: 96px;
            height: 96px;
            margin: 6px auto 14px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 42px;
        }

        .rs-result .badge-icon.spin i { animation: rs-spin 1.2s linear infinite; }

        @keyframes rs-spin { to { transform: rotate(360deg); } }

        .rs-result h4 { font-size: 19px; font-weight: 800; margin-bottom: 6px; }
        .rs-result p { color: #6c757d; font-size: 14.5px; line-height: 1.9; max-width: 460px; margin: 0 auto; }

        /* ===== المراحل ===== */
        .rs-steps {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin: 14px 0 26px;
        }

        .rs-steps::before {
            content: "";
            position: absolute;
            top: 21px;
            right: 30px;
            left: 30px;
            height: 2px;
            background: #e3f0ed;
        }

        .rs-step { position: relative; z-index: 1; text-align: center; width: 20%; }

        .rs-step .icon {
            width: 42px;
            height: 42px;
            margin: 0 auto 8px;
            border-radius: 50%;
            background: #fff;
            border: 2px solid #cfe6e1;
            color: #9bb9b3;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
            transition: all 0.3s ease;
        }

        .rs-step span { font-size: 12px; color: #495057; font-weight: 600; }

        .rs-step.done .icon { background: #008870; border-color: #008870; color: #fff; }

        .rs-step.current .icon {
            border-color: #e8a33d;
            color: #e8a33d;
            box-shadow: 0 0 0 5px rgba(232, 163, 61, 0.15);
        }

        .rs-step.error .icon { background: #d63031; border-color: #d63031; color: #fff; }

        /* ===== التفاصيل ===== */
        .rs-details {
            border: 1px solid #e3f0ed;
            border-radius: 14px;
            overflow: hidden;
            margin-bottom: 20px;
        }

        .rs-details .row-item {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            padding: 11px 16px;
            font-size: 14px;
            border-bottom: 1px solid #f0f5f4;
        }

        .rs-details .row-item:last-child { border-bottom: none; }
        .rs-details .row-item span { color: #6c757d; white-space: nowrap; }
        .rs-details .row-item b { color: #000; word-break: break-all; text-align: left; }

        .rs-actions { display: flex; gap: 10px; flex-wrap: wrap; }

        .rs-actions a {
            flex: 1;
            min-width: 180px;
            text-align: center;
            padding: 12px 16px;
            border-radius: 10px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.25s ease;
        }

        .rs-actions .primary { background: #008870; color: #fff; }
        .rs-actions .outline { border: 1px solid #d63031; color: #d63031; }
        .rs-actions .outline:hover { background: #d63031; color: #fff; }

        @media (max-width: 576px) {
            .rs-step span { font-size: 10.5px; }
            .rs-step .icon { width: 36px; height: 36px; font-size: 15px; }
            .rs-steps::before { top: 18px; }
        }
    </style>
@endsection

@section('content')
@php
    $info = $restore->statusInfo();
    $steps = [
        1 => ['icon' => 'bi-search', 'label' => 'فحص النسخة'],
        2 => ['icon' => 'bi-life-preserver', 'label' => 'نسخة أمان'],
        3 => ['icon' => 'bi-database-down', 'label' => 'الاسترجاع'],
        4 => ['icon' => 'bi-diagram-3', 'label' => 'تحديث الجداول'],
        5 => ['icon' => 'bi-check2', 'label' => 'تم'],
    ];
@endphp

<section id="contact" class="contact section">
    <div class="col-lg-8 offset-lg-2 text-center">
        <div class="section-title">
            <h3>عملية <span class="orange-text">الاسترجاع #{{ $restore->id }}</span></h3>
        </div>
    </div>

    <div class="container my-5" style="direction: rtl; text-align: right">
        <div class="row justify-content-center">
            <div class="col-14 col-md-10 col-lg-8">
                <div class="form-container">

                    <div class="d-flex flex-md-row justify-content-between align-items-center mb-3">
                        <h3>{{ $restore->undo_of ? 'تراجع عن استرجاع' : 'استرجاع نسخة' }}</h3>
                        <a href="{{ url('backup-restores') }}">
                            <i class="bi bi-arrow-left fs-4 text-dark"></i>
                        </a>
                    </div>

                    {{-- النتيجة --}}
                    <div class="rs-result">
                        <div class="badge-icon {{ $restore->isActive() ? 'spin' : '' }}"
                            style="background: {{ $info['bg'] }}; color: {{ $info['color'] }};">
                            <i class="bi {{ $info['icon'] }}"></i>
                        </div>
                        <h4 style="color: {{ $info['color'] }}">{{ $info['label'] }}</h4>
                        <p>{{ $restore->message }}</p>
                    </div>

                    {{-- المراحل --}}
                    <div class="rs-steps">
                        @foreach ($steps as $num => $step)
                            @php
                                $state = '';
                                if ($restore->status === 'success' || $restore->status === 'undone' || $num < $restore->step) {
                                    $state = 'done';
                                } elseif ($num === $restore->step) {
                                    $state = $restore->status === 'failed' ? 'error' : 'current';
                                }
                            @endphp
                            <div class="rs-step {{ $state }}">
                                <div class="icon"><i class="bi {{ $step['icon'] }}"></i></div>
                                <span>{{ $step['label'] }}</span>
                            </div>
                        @endforeach
                    </div>

                    {{-- التفاصيل --}}
                    <div class="rs-details">
                        <div class="row-item"><span>النسخة المسترجعة</span><b>{{ $restore->backup_file }}</b></div>
                        <div class="row-item"><span>نسخة الأمان</span><b>{{ $restore->safety_file ?? '—' }}</b></div>
                        <div class="row-item"><span>بواسطة</span><b>{{ $restore->userName() }}</b></div>
                        <div class="row-item"><span>البداية</span><b>{{ $restore->started_at?->format('Y-m-d h:i:s A') ?? '—' }}</b></div>
                        <div class="row-item"><span>النهاية</span><b>{{ $restore->finished_at?->format('Y-m-d h:i:s A') ?? '—' }}</b></div>
                    </div>

                    {{-- الأزرار --}}
                    @unless ($restore->isActive())
                        <div class="rs-actions">
                            <a href="{{ url('backup-restores') }}" class="primary">
                                <i class="bi bi-list-ul ms-1"></i> سجل عمليات الاسترجاع
                            </a>
                            @if ($canUndo)
                                <a href="{{ url('/restore-backup/' . $restore->safety_file . '?undo=' . $restore->id) }}" class="outline">
                                    <i class="bi bi-arrow-counterclockwise ms-1"></i> تراجع عن الاسترجاع
                                </a>
                            @endif
                        </div>
                    @endunless

                </div>
            </div>
        </div>
    </div>
</section>
@endsection
