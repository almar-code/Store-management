@extends('Layouts.master')

@section('link')
    <style>
        /* ===== أيقونة التحذير ===== */
        .rs-hero { text-align: center; padding: 6px 0 22px; }

        .rs-warn {
            position: relative;
            width: 120px;
            height: 120px;
            margin: 6px auto 16px;
        }

        .rs-warn .core {
            position: absolute;
            inset: 14px;
            border-radius: 50%;
            background: linear-gradient(145deg, #e8a33d, #d63031);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 40px;
            box-shadow: 0 12px 30px rgba(214, 48, 49, 0.3);
            z-index: 2;
        }

        .rs-warn .ring {
            position: absolute;
            inset: 0;
            border-radius: 50%;
            border: 2px solid rgba(214, 48, 49, 0.3);
            animation: rs-pulse 2.6s ease-out infinite;
        }

        .rs-warn .ring:nth-child(2) { animation-delay: 1.3s; }

        @keyframes rs-pulse {
            0%   { transform: scale(0.8); opacity: 1; }
            100% { transform: scale(1.35); opacity: 0; }
        }

        .rs-hero p {
            color: #6c757d;
            font-size: 15px;
            max-width: 440px;
            margin: 0 auto;
            line-height: 1.9;
        }

        /* ===== بطاقة النسخة ===== */
        .rs-file {
            display: flex;
            align-items: center;
            gap: 12px;
            border: 1px dashed #cfe6e1;
            background: #f8fbfa;
            border-radius: 14px;
            padding: 14px 16px;
            margin-bottom: 18px;
        }

        .rs-file .icon {
            width: 46px;
            height: 46px;
            flex-shrink: 0;
            border-radius: 12px;
            background: #e6f5f2;
            color: #008870;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 21px;
        }

        .rs-file strong { display: block; font-size: 15px; color: #000; }
        .rs-file small { color: #6c757d; font-size: 13px; word-break: break-all; }

        .rs-chip {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 700;
            margin-inline-start: 6px;
        }

        /* ===== البيانات اللي راح تضيع ===== */
        .rs-section-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 15px;
            font-weight: 700;
            margin: 4px 0 10px;
        }

        .rs-section-label i { color: #d63031; font-size: 18px; }

        .rs-impact {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 18px;
        }

        .rs-impact .item {
            background: #fdf3f3;
            border: 1px solid #f6d6d6;
            border-radius: 12px;
            padding: 12px 8px;
            text-align: center;
        }

        .rs-impact .item.zero { background: #f8fbfa; border-color: #e3f0ed; }

        .rs-impact .value { display: block; font-size: 20px; font-weight: 800; color: #d63031; }
        .rs-impact .item.zero .value { color: #008870; }
        .rs-impact .label { font-size: 12.5px; color: #6c757d; }

        /* ===== طبقات الحماية ===== */
        .rs-shields {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px 14px;
            background: #f8fbfa;
            border: 1px solid #e3f0ed;
            border-radius: 14px;
            padding: 14px 16px;
            margin-bottom: 20px;
        }

        .rs-shields div { display: flex; align-items: flex-start; gap: 8px; font-size: 13.5px; color: #495057; line-height: 1.7; }
        .rs-shields i { color: #008870; font-size: 16px; margin-top: 2px; }

        /* ===== حقول التأكيد ===== */
        .rs-confirm-hint { font-size: 13px; color: #6c757d; margin: -4px 0 8px; }
        .rs-confirm-hint b { color: #d63031; }

        .rs-running {
            display: flex;
            gap: 10px;
            align-items: flex-start;
            background: #fff8ec;
            border: 1px solid #ffe2b3;
            border-radius: 12px;
            padding: 12px 14px;
            font-size: 14px;
            color: #7a5a1d;
            margin-bottom: 16px;
        }

        .btn-danger-submit {
            background: linear-gradient(135deg, #e05a4f, #d63031) !important;
        }

        @media (max-width: 576px) {
            .rs-impact { gap: 6px; }
            .rs-impact .value { font-size: 17px; }
            .rs-shields { grid-template-columns: 1fr; }
        }
    </style>
@endsection

@section('content')
<section id="contact" class="contact section">
    <div class="col-lg-8 offset-lg-2 text-center">
        <div class="section-title">
            <h3>{{ $undo ? 'تراجع عن' : 'استرجاع' }} <span class="orange-text">{{ $undo ? 'الاسترجاع' : 'نسخة احتياطية' }}</span></h3>
        </div>
    </div>

    <div class="container my-5" style="direction: rtl; text-align: right">
        <div class="row justify-content-center">
            <div class="col-14 col-md-10 col-lg-8">
                <div class="form-container">

                    <div class="d-flex flex-md-row justify-content-between align-items-center mb-3">
                        <h3>تأكيد العملية</h3>
                        <a href="{{ url()->previous() }}">
                            <i class="bi bi-arrow-left fs-4 text-dark"></i>
                        </a>
                    </div>

                    {{-- التحذير --}}
                    <div class="rs-hero">
                        <div class="rs-warn">
                            <span class="ring"></span>
                            <span class="ring"></span>
                            <div class="core"><i class="bi bi-arrow-counterclockwise"></i></div>
                        </div>
                        <p>
                            @if ($undo)
                                سترجع بيانات المتجر كما كانت قبل عملية الاسترجاع رقم <b>{{ $undo->id }}</b>.
                            @else
                                ستُستبدل بيانات المتجر الحالية ببيانات هذه النسخة. ستأخذ المنصة نسخة أمان أولاً، وتقدر تتراجع في أي وقت.
                            @endif
                        </p>
                    </div>

                    {{-- النسخة المختارة --}}
                    <div class="rs-file">
                        <div class="icon"><i class="bi bi-file-earmark-lock"></i></div>
                        <div>
                            <strong>
                                نسخة {{ $date->format('Y-m-d') }} — {{ $date->format('h:i A') }}
                                @if ($onCloud)
                                    <span class="rs-chip" style="background:#e8eef6; color:#1a4372;">من السحابة</span>
                                @endif
                            </strong>
                            <small>{{ $file }}</small>
                        </div>
                    </div>

                    {{-- البيانات اللي راح تضيع --}}
                    @if (count($impact))
                        <div class="rs-section-label">
                            <i class="bi bi-exclamation-triangle"></i> بيانات أُضيفت بعد هذه النسخة وستُحذف
                        </div>
                        <div class="rs-impact">
                            @foreach ($impact as $item)
                                <div class="item {{ $item['count'] ? '' : 'zero' }}">
                                    <span class="value">{{ $item['count'] }}</span>
                                    <span class="label">{{ $item['label'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- طبقات الحماية --}}
                    <div class="rs-shields">
                        <div><i class="bi bi-shield-check"></i> فحص النسخة قبل استخدامها</div>
                        <div><i class="bi bi-life-preserver"></i> نسخة أمان تلقائية قبل الاسترجاع</div>
                        <div><i class="bi bi-cone-striped"></i> وضع صيانة مؤقت أثناء العملية</div>
                        <div><i class="bi bi-arrow-counterclockwise"></i> إمكانية التراجع من سجل العمليات</div>
                    </div>

                    @if ($running)
                        <div class="rs-running">
                            <i class="bi bi-hourglass-split"></i>
                            <div>توجد عملية استرجاع جارية الآن، انتظر حتى تنتهي ثم أعد المحاولة.</div>
                        </div>
                    @endif

                    <form id="dataForm" action="{{ url('/restore-backup/' . $file) }}" method="post" autocomplete="off">
                        @csrf

                        @if ($undo)
                            <input type="hidden" name="undo_of" value="{{ $undo->id }}">
                        @endif

                        {{-- كلمة المرور --}}
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="password" name="password"
                                placeholder="كلمة المرور" autocomplete="current-password" required>
                            <label for="password">كلمة مرور حسابك</label>
                            @error('password')
                                <div class="form-error">
                                    <i class="bi bi-exclamation-circle"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- كلمة التأكيد --}}
                        <p class="rs-confirm-hint">للتأكيد اكتب كلمة <b>استرجاع</b> في الحقل التالي</p>
                        <div class="form-floating mb-4">
                            <input type="text" class="form-control" id="confirm_text" name="confirm_text"
                                placeholder="استرجاع" value="{{ old('confirm_text') }}" required>
                            <label for="confirm_text">كلمة التأكيد</label>
                            @error('confirm_text')
                                <div class="form-error">
                                    <i class="bi bi-exclamation-circle"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="d-grid" style="direction: ltr">
                            <button type="submit" class="btn-submit btn-danger-submit" id="saveBtn"
                                {{ $running ? 'disabled' : '' }}
                                onclick="activateLoading('dataForm', 'saveBtn');">
                                {{ $undo ? 'تأكيد التراجع' : 'تأكيد الاسترجاع' }}
                                <i class="bi bi-arrow-counterclockwise ms-2"></i>
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</section>
@endsection
