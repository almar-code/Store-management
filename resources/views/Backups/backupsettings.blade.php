@extends('Layouts.master')

@section('link')
    <style>
        /* نفس أزرار الراديو المستخدمة في صفحات الإضافة */
        .radio-buttons-container {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 24px;
            padding: 10px 0 20px;
        }
        .radio-button { display: inline-block; position: relative; cursor: pointer; }
        .radio-button__input { position: absolute; opacity: 0; width: 0; height: 0; }
        .radio-button__label {
            display: inline-block;
            padding-left: 30px;
            margin-bottom: 10px;
            position: relative;
            font-size: 16px;
            color: #000000;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.23, 1, 0.320, 1);
        }
        .radio-button__custom {
            position: absolute;
            top: 50%;
            left: 0;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 2px solid #000000;
            transition: all 0.3s cubic-bezier(0.23, 1, 0.320, 1);
        }
        .radio-button__input:checked + .radio-button__label .radio-button__custom {
            transform: translateY(-50%) scale(0.9);
            border: 5px solid #008870;
            color: #008870;
        }
        .radio-button__input:checked + .radio-button__label { color: #008870; }
        .radio-button__label:hover .radio-button__custom {
            transform: translateY(-50%) scale(1.2);
            border-color: #008870;
            box-shadow: 0 0 10px #36b8a0;
        }

        /* ===== عناوين الأقسام ===== */
        .bk-section-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 15px;
            font-weight: 700;
            color: #000;
            margin: 6px 0 12px;
        }

        .bk-section-label i { color: #008870; font-size: 18px; }

        .bk-hint {
            font-size: 13px;
            color: #6c757d;
            line-height: 1.8;
            margin: -4px 0 14px;
        }

        /* ===== بطاقات مدة الاحتفاظ ===== */
        .bk-keep-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 24px;
        }

        .bk-keep {
            border: 1px solid #e3f0ed;
            border-radius: 14px;
            padding: 14px;
            background: #f8fbfa;
            transition: all 0.25s ease;
        }

        .bk-keep:focus-within {
            border-color: #008870;
            background: #fff;
            box-shadow: 0 6px 18px rgba(0, 136, 112, 0.12);
        }

        .bk-keep .head {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .bk-keep .head .icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: #e6f5f2;
            color: #008870;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
        }

        .bk-keep .head span {
            font-weight: 700;
            font-size: 14.5px;
            color: #000;
        }

        .bk-keep .field {
            display: flex;
            align-items: center;
            border: 1px solid #dfe9e7;
            border-radius: 10px;
            background: #fff;
            overflow: hidden;
        }

        .bk-keep .field input {
            border: none;
            outline: none;
            width: 100%;
            padding: 9px 12px;
            font-size: 16px;
            font-weight: 600;
            text-align: center;
            background: transparent;
        }

        .bk-keep .field .unit {
            padding: 9px 12px;
            background: #f1f8f6;
            color: #008870;
            font-size: 13px;
            font-weight: 700;
            white-space: nowrap;
        }

        @media (max-width: 576px) {
            .bk-keep-grid { grid-template-columns: 1fr; }
        }
    </style>
@endsection

@section('content')
<section id="contact" class="contact section">
    <div class="col-lg-8 offset-lg-2 text-center">
        <div class="section-title">
            <h3>إعدادات <span class="orange-text">النسخ الاحتياطي</span></h3>
        </div>
    </div>

    <div class="container my-5" style="direction: rtl; text-align: right">
        <div class="row justify-content-center">
            <div class="col-14 col-md-10 col-lg-8">
                <div class="form-container">

                    <div class="d-flex flex-md-row justify-content-between align-items-center mb-3">
                        <h3>النسخ التلقائي</h3>
                        <a href="{{ url()->previous() }}">
                            <i class="bi bi-arrow-left fs-4 text-dark"></i>
                        </a>
                    </div>

                    <form id="dataForm" action="{{ url('/update-backup-settings') }}" method="post">
                        @csrf

                        {{-- 1. تفعيل / إيقاف --}}
                        <div class="radio-buttons-container">
                            <div class="radio-button">
                                <input name="enabled" value="1" id="radio2" class="radio-button__input" type="radio"
                                    {{ old('enabled', (int) $settings->enabled) == '1' ? 'checked' : '' }}>
                                <label for="radio2" class="radio-button__label">
                                    <span class="radio-button__custom"></span>
                                    مفعّل
                                </label>
                            </div>

                            <div class="radio-button">
                                <input name="enabled" value="0" id="radio1" class="radio-button__input" type="radio"
                                    {{ old('enabled', (int) $settings->enabled) == '0' ? 'checked' : '' }}>
                                <label for="radio1" class="radio-button__label">
                                    <span class="radio-button__custom"></span>
                                    متوقف
                                </label>
                            </div>
                        </div>

                        {{-- 2. وقت النسخ اليومي --}}
                        <div class="form-floating mb-4">
                            <input type="time" class="form-control" id="run_time" name="run_time"
                                value="{{ old('run_time', $settings->run_time) }}" required>
                            <label for="run_time">وقت النسخ اليومي</label>
                            @error('run_time')
                                <div class="form-error">
                                    <i class="bi bi-exclamation-circle"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- 3. مدة الاحتفاظ --}}
                        <div class="bk-section-label">
                            <i class="bi bi-hourglass-split"></i> مدة الاحتفاظ بالنسخ
                        </div>
                        <p class="bk-hint">
                            يحتفظ النظام بكل النسخ لآخر 7 أيام، ثم يبقي نسخة واحدة لكل يوم، ثم لكل أسبوع، ثم لكل شهر، ثم لكل سنة، ويحذف الأقدم تلقائياً.
                        </p>

                        <div class="bk-keep-grid">
                            @foreach ($types as $type)
                                <div class="bk-keep">
                                    <div class="head">
                                        <div class="icon"><i class="bi {{ $type['icon'] }}"></i></div>
                                        <span>النسخ {{ $type['title'] }}</span>
                                    </div>
                                    <div class="field">
                                        <input type="number" min="1" name="{{ $type['field'] }}"
                                            value="{{ old($type['field'], $settings->{$type['field']}) }}" required>
                                        <span class="unit">{{ $type['unit'] }}</span>
                                    </div>
                                    @error($type['field'])
                                        <div class="form-error">
                                            <i class="bi bi-exclamation-circle"></i> {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            @endforeach
                        </div>

                        {{-- زر الحفظ --}}
                        <div class="d-grid" style="direction: ltr">
                            <button type="submit" class="btn-submit" id="saveBtn" onclick="activateLoading('dataForm', 'saveBtn');">
                                حفظ الإعدادات <i class="bi bi-check-lg ms-2"></i>
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</section>
@endsection
