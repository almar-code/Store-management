@extends('Layouts.master')

@section('link')
    <style>
        /* ===== بطاقة الحماية الرئيسية ===== */
        .backup-hero {
            text-align: center;
            padding: 10px 0 25px;
        }

        .backup-shield {
            position: relative;
            width: 130px;
            height: 130px;
            margin: 10px auto 18px;
        }

        .backup-shield .core {
            position: absolute;
            inset: 15px;
            border-radius: 50%;
            background: linear-gradient(145deg, #008870, color-mix(in srgb, #008870, #1a4372 30%));
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 42px;
            box-shadow: 0 12px 30px rgba(0, 136, 112, 0.35);
            z-index: 2;
        }

        .backup-shield .ring {
            position: absolute;
            inset: 0;
            border-radius: 50%;
            border: 2px solid rgba(0, 136, 112, 0.35);
            animation: backup-pulse 2.6s ease-out infinite;
        }

        .backup-shield .ring:nth-child(2) { animation-delay: 1.3s; }

        @keyframes backup-pulse {
            0%   { transform: scale(0.8); opacity: 1; }
            100% { transform: scale(1.35); opacity: 0; }
        }

        .backup-hero p {
            color: #6c757d;
            font-size: 15px;
            max-width: 420px;
            margin: 0 auto;
            line-height: 1.9;
        }

        /* ===== شارة حالة آخر نسخة ===== */
        .backup-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 600;
            margin-top: 14px;
        }

        .backup-pill.success { background: #e6f5f2; color: #008870; }
        .backup-pill.failed  { background: #fdecec; color: #d63031; }
        .backup-pill.none    { background: #f1f3f5; color: #6c757d; }

        .backup-pill .dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: currentColor;
        }

        /* ===== الإحصائيات ===== */
        .backup-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 26px;
        }

        .backup-stat {
            background: #f8fbfa;
            border: 1px solid #e3f0ed;
            border-radius: 14px;
            padding: 14px 10px;
            text-align: center;
        }

        .backup-stat i {
            font-size: 22px;
            color: #008870;
        }

        .backup-stat .value {
            display: block;
            font-size: 18px;
            font-weight: 700;
            color: #000;
            margin-top: 4px;
        }

        .backup-stat .label {
            font-size: 12px;
            color: #6c757d;
        }

        /* ===== مراحل إنشاء النسخة ===== */
        .backup-steps {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin: 10px 0 28px;
            padding: 0 6px;
        }

        .backup-steps::before {
            content: "";
            position: absolute;
            top: 23px;
            right: 40px;
            left: 40px;
            height: 2px;
            background: #e3f0ed;
            z-index: 0;
        }

        .backup-step {
            position: relative;
            z-index: 1;
            text-align: center;
            width: 25%;
        }

        .backup-step .icon {
            width: 46px;
            height: 46px;
            margin: 0 auto 8px;
            border-radius: 50%;
            background: #fff;
            border: 2px solid #cfe6e1;
            color: #008870;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 19px;
            transition: all 0.4s ease;
        }

        .backup-step span {
            font-size: 12.5px;
            color: #495057;
            font-weight: 600;
        }

        /* تتلوّن المراحل بالتتابع عند الضغط على الزر */
        .backup-steps.running .backup-step .icon {
            animation: backup-step-on 0.5s ease forwards;
        }

        .backup-steps.running .backup-step:nth-child(1) .icon { animation-delay: 0.1s; }
        .backup-steps.running .backup-step:nth-child(2) .icon { animation-delay: 0.6s; }
        .backup-steps.running .backup-step:nth-child(3) .icon { animation-delay: 1.1s; }
        .backup-steps.running .backup-step:nth-child(4) .icon { animation-delay: 1.6s; }

        @keyframes backup-step-on {
            to {
                background: #008870;
                border-color: #008870;
                color: #fff;
                box-shadow: 0 6px 16px rgba(0, 136, 112, 0.35);
            }
        }

        /* ===== تنبيه الاسترجاع ===== */
        .backup-note {
            display: flex;
            gap: 10px;
            align-items: flex-start;
            background: #fff8ec;
            border: 1px solid #ffe2b3;
            border-radius: 12px;
            padding: 12px 14px;
            font-size: 13.5px;
            color: #7a5a1d;
            margin-bottom: 22px;
            line-height: 1.8;
        }

        .backup-note i { font-size: 18px; color: #e8a33d; }

        /* ===== بطاقة آخر نسخة مع زر التحميل ===== */
        .backup-latest {
            display: flex;
            align-items: center;
            gap: 12px;
            border: 1px dashed #b9ddd6;
            border-radius: 14px;
            padding: 12px 14px;
            margin-bottom: 22px;
            background: #fbfefd;
        }

        .backup-latest .file-icon {
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

        .backup-latest .info { flex: 1; min-width: 0; }

        .backup-latest .info strong {
            display: block;
            font-size: 14.5px;
            color: #000;
        }

        .backup-latest .info small { color: #6c757d; font-size: 12.5px; }

        .backup-latest .download {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 10px;
            background: #008870;
            color: #fff;
            font-size: 13.5px;
            font-weight: 600;
            text-decoration: none;
            white-space: nowrap;
            transition: all 0.25s ease;
        }

        .backup-latest .download:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 14px rgba(0, 136, 112, 0.3);
            color: #fff;
        }

        @media (max-width: 576px) {
            .backup-stats { grid-template-columns: 1fr 1fr 1fr; gap: 8px; }
            .backup-stat .value { font-size: 15px; }
            .backup-step span { font-size: 11px; }
        }
    </style>
@endsection

@section('content')
<section id="contact" class="contact section">
    <div class="col-lg-8 offset-lg-2 text-center">
        <div class="section-title">
            <h3>إنشاء <span class="orange-text">نسخة احتياطية</span></h3>
        </div>
    </div>

    <div class="container my-5" style="direction: rtl; text-align: right">
        <div class="row justify-content-center">
            <div class="col-14 col-md-10 col-lg-8">
                <div class="form-container">

                    <div class="d-flex flex-md-row justify-content-between align-items-center mb-3">
                        <h3>نسخة احتياطية جديدة</h3>
                        <a href="{{ url()->previous() }}">
                            <i class="bi bi-arrow-left fs-4 text-dark"></i>
                        </a>
                    </div>

                    {{-- البطاقة الرئيسية --}}
                    <div class="backup-hero">
                        <div class="backup-shield">
                            <span class="ring"></span>
                            <span class="ring"></span>
                            <div class="core"><i class="bi bi-shield-lock"></i></div>
                        </div>

                        <p>احفظ نسخة كاملة ومشفّرة من بيانات المتجر الآن: المنتجات، الطلبات، العملاء وكل الإعدادات.</p>

                        @if ($settings->last_backup_status === 'success')
                            <div class="backup-pill success">
                                <span class="dot"></span>
                                آخر نسخة نجحت — {{ $settings->last_backup_at?->format('Y-m-d h:i A') }}
                            </div>
                        @elseif ($settings->last_backup_status === 'failed')
                            <div class="backup-pill failed">
                                <span class="dot"></span>
                                آخر نسخة فشلت — تواصل مع الدعم الفني
                            </div>
                        @else
                            <div class="backup-pill none">
                                <span class="dot"></span>
                                لم تُنشأ أي نسخة بعد
                            </div>
                        @endif
                    </div>

                    {{-- الإحصائيات --}}
                    <div class="backup-stats">
                        <div class="backup-stat">
                            <i class="bi bi-archive"></i>
                            <span class="value">{{ $stats['count'] }}</span>
                            <span class="label">عدد النسخ</span>
                        </div>
                        <div class="backup-stat">
                            <i class="bi bi-hdd"></i>
                            <span class="value">{{ $stats['size'] }}</span>
                            <span class="label">الحجم الكلي</span>
                        </div>
                        <div class="backup-stat">
                            <i class="bi bi-clock-history"></i>
                            <span class="value">{{ $settings->enabled ? $settings->run_time : 'متوقف' }}</span>
                            <span class="label">النسخ التلقائي</span>
                        </div>
                    </div>

                    {{-- مراحل النسخ --}}
                    <div class="backup-steps" id="backupSteps">
                        <div class="backup-step">
                            <div class="icon"><i class="bi bi-database"></i></div>
                            <span>تصدير البيانات</span>
                        </div>
                        <div class="backup-step">
                            <div class="icon"><i class="bi bi-file-earmark-zip"></i></div>
                            <span>ضغط الملف</span>
                        </div>
                        <div class="backup-step">
                            <div class="icon"><i class="bi bi-lock"></i></div>
                            <span>تشفير AES-256</span>
                        </div>
                        <div class="backup-step">
                            <div class="icon"><i class="bi bi-cloud-check"></i></div>
                            <span>حفظ آمن</span>
                        </div>
                    </div>

                    {{-- آخر نسخة مع زر التحميل --}}
                    @if ($latest)
                        <div class="backup-latest">
                            <div class="file-icon"><i class="bi bi-file-earmark-lock"></i></div>
                            <div class="info">
                                <strong>
                                    آخر نسخة
                                    <span class="badge ms-1" style="{{ $latest['manual'] ? 'background:#fff3e0; color:#e8a33d;' : 'background:#e6f5f2; color:#008870;' }}">
                                        {{ $latest['manual'] ? 'يدوية' : 'تلقائية' }}
                                    </span>
                                </strong>
                                <small>{{ $latest['date']->format('Y-m-d h:i A') }} — {{ $latest['size'] }}</small>
                            </div>
                            <a href="{{ url('/backup-download/' . $latest['file']) }}" class="download">
                                <i class="bi bi-download"></i> تحميل
                            </a>
                        </div>
                    @endif

                    {{-- تنبيه الاسترجاع --}}
                    <div class="backup-note">
                        <i class="bi bi-info-circle-fill"></i>
                        <div>
                            إنشاء النسخة لا يغيّر أي بيانات في المتجر.
                            {الاسترجاع متاح من صفحات النسخ، مع <strong>نسخة أمان تلقائية</strong> وإمكانية التراجع في أي وقت.}                        </div>
                    </div>

                    <form id="dataForm" action="{{ url('/add-backup') }}" method="post">
                        @csrf
                        <div class="d-grid" style="direction: ltr">
                            <button type="submit" class="btn-submit" id="saveBtn"
                                onclick="document.getElementById('backupSteps').classList.add('running'); activateLoading('dataForm', 'saveBtn');">
                                إنشاء نسخة الآن <i class="bi bi-cloud-arrow-up ms-2"></i>
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</section>
@endsection
