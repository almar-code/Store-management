@extends('Layouts.master')

@section('link')
    <style>
        .radio-buttons-container {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 24px;
            padding: 20px 0;
        }

        .radio-button {
            display: inline-block;
            position: relative;
            cursor: pointer;
        }

        .radio-button__input {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

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

        .radio-button__input:checked+.radio-button__label .radio-button__custom {
            transform: translateY(-50%) scale(0.9);
            border: 5px solid #008870;
            color: #008870;
        }

        .radio-button__input:checked+.radio-button__label {
            color: #008870;
        }

        .radio-button__label:hover .radio-button__custom {
            transform: translateY(-50%) scale(1.2);
            border-color: #008870;
            box-shadow: 0 0 10px #36b8a0;
        }
    </style>
@endsection

@section('content')
<section id="contact" class="contact section">
    <div class="col-lg-8 offset-lg-2 text-center">
        <div class="section-title">
            <h3>{{ isset($account) ? 'تعديل' : 'إضافة' }} <span class="orange-text">حساب دفع</span></h3>
        </div>
    </div>
   
    <div class="container my-5" style="direction: rtl; text-align: right">
      <div class="row justify-content-center">
        <div class="col-14 col-md-10 col-lg-8">
            <div class="form-container">

                <div class="d-flex flex-md-row justify-content-between align-items-center mb-3">
                    <h3>
                        {{ isset($account) ? 'تعديل حساب الدفع' : 'إضافة حساب دفع جديد' }}
                    </h3>  
                    <a href="{{ url()->previous() }}">
                        <i class="bi bi-arrow-left fs-4 text-dark"></i>
                    </a>
                </div>
                
                {{-- تحديد المايجريشن المسار (Route) بناءً على الإضافة أو التعديل --}}
                <form id="dataForm" action="{{ isset($account) ? url('/update-store-payment-account/' . $account->store_payment_account_id) : url('/add-store-payment-account') }}" method="post">
                    @csrf
                    @if(isset($account))
                        @method('PUT')
                    @endif

                    {{-- 1. نوع الحساب (قائمة منسدلة) --}}
                    <div class="form-floating mb-3">
                        <select class="form-select" id="account_type" name="account_type" required>
                            <option value="" disabled {{ !old('account_type', $account->account_type ?? '') ? 'selected' : '' }}>اختر نوع الحساب...</option>
                            <option value="bank" {{ old('account_type', $account->account_type ?? '') == 'bank' ? 'selected' : '' }}>حساب بنكي (Bank)</option>
                            <option value="wallet" {{ old('account_type', $account->account_type ?? '') == 'wallet' ? 'selected' : '' }}>محفظة إلكترونية (Wallet)</option>
                        </select>
                        <label for="account_type">نوع الحساب</label>
                        @error('account_type')
                            <div class="form-error text-danger mt-1">
                                <i class="bi bi-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- 2. اسم المزود / البنك / المحفظة --}}
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="provider_name" name="provider_name" placeholder="اسم المزود" value="{{ old('provider_name', $account->provider_name ?? '') }}" required>
                        <label for="provider_name">اسم البنك / المحفظة (المزود)</label>
                        @error('provider_name')
                            <div class="form-error text-danger mt-1">
                                <i class="bi bi-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- 3. اسم صاحب الحساب (بالعربي) --}}
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="account_holder_name" name="account_holder_name" placeholder="مالك الحساب" value="{{ old('account_holder_name', $account->account_holder_name ?? '') }}" required>
                        <label for="account_holder_name">اسم صاحب الحساب (بالعربي)</label>
                        @error('account_holder_name')
                            <div class="form-error text-danger mt-1">
                                <i class="bi bi-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>
                    {{-- 5. رقم الحساب --}}
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="account_number" name="account_number" placeholder="رقم الحساب" value="{{ old('account_number', $account->account_number ?? '') }}">
                        <label for="account_number">رقم الحساب</label>
                        @error('account_number')
                            <div class="form-error text-danger mt-1">
                                <i class="bi bi-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="iban" name="iban" placeholder="IBAN" value="{{ old('iban', $account->iban ?? '') }}">
                        <label for="iban">رقم الآيبان (IBAN)</label>
                        @error('iban')
                            <div class="form-error text-danger mt-1">
                                <i class="bi bi-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div> --}}

                    {{-- 7. تعليمات إضافية --}}
                    {{-- <div class="form-floating mb-3">
                        <textarea class="form-control" id="instructions" name="instructions" placeholder="تعليمات الدفع" style="height: 100px">{{ old('instructions', $account->instructions ?? '') }}</textarea>
                        <label for="instructions">تعليمات التحويل / ملاحظات</label>
                        @error('instructions')
                            <div class="form-error text-danger mt-1">
                                <i class="bi bi-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div> --}}

                    {{-- 8. ترتيب العرض --}}
                    {{-- <div class="form-floating mb-3">
                        <input type="number" class="form-control" id="sort_order" name="sort_order" placeholder="ترتيب العرض" value="{{ old('sort_order', $account->sort_order ?? 0) }}">
                        <label for="sort_order">ترتيب العرض</label>
                        @error('sort_order')
                            <div class="form-error text-danger mt-1">
                                <i class="bi bi-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div> --}}

                    {{-- 9. حالة الحساب (مفعل / غير مفعل) --}}
                    <div class="radio-buttons-container">
                        <div class="radio-button">
                            <input name="is_active" value="1" id="radio2" class="radio-button__input" type="radio" {{ old('is_active', $account->is_active ?? '1') == "1" ? 'checked' : '' }}>
                            <label for="radio2" class="radio-button__label">
                                <span class="radio-button__custom"></span>
                                مفعل 
                            </label>
                        </div>

                        <div class="radio-button">
                            <input name="is_active" value="0" id="radio1" class="radio-button__input" type="radio" {{ old('is_active', $account->is_active ?? '1') == "0" ? 'checked' : '' }}>
                            <label for="radio1" class="radio-button__label">
                                <span class="radio-button__custom"></span>
                                غير مفعل
                            </label>
                        </div>
                        @error('is_active')
                            <div class="form-error text-danger mt-1">
                                <i class="bi bi-exclamation-circle"></i> يرجى اختيار حالة الحساب
                            </div>
                        @enderror
                    </div>

                    {{-- زر الحفظ --}}
                    <div class="d-grid" style="direction: ltr">
                        <button type="submit" class="btn-submit" id="saveBtn" onclick="activateLoading('dataForm', 'saveBtn');">
                            {{ isset($account) ? 'تعديل الحساب' : 'حفظ الحساب' }} <i class="bi bi-check-lg ms-2"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div> 
      </div> 
    </div>
</section>
@endsection