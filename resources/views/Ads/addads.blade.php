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
            <h3>Add <span class="orange-text">Advertisement</span></h3>
        </div>
    </div>
   
    <div class="container my-5" style="direction: rtl; text-align: right">
      <div class="row justify-content-center">
        <div class="col-14 col-md-10 col-lg-8">
            <div class="form-container">

                  <div class="d-flex flex-md-row justify-content-between align-items-center mb-3">
                    <h3>
                    {{ isset($product) ? 'إضافة إعلان منتج' : (isset($isLinkAd) && $isLinkAd ? 'إعلان برابط' : 'إضافة إعلان') }}
                </h3>  
                       <a href="{{ url()->previous() }}">
                            <i class="bi bi-arrow-left fs-4 text-dark"></i>
                        </a>
                    </div>
                
              
                {{-- تحديد مسار الـ Form بناءً على ما إذا كنا نضيف إعلان عادي أو إعلان خاص بمنتج --}}
                <form id="dataForm" action="{{ isset($product) ? url('/create-ad/' . $product->p_id) : url('/add-ads') }}" method="post" enctype="multipart/form-data">
                    @csrf   
                    
                    {{-- إذا كنا في صفحة إعلان منتج، يتم عرض اسم المنتج للقراءة فقط --}}
                    @if(isset($product))
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control bg-light" id="product_name" value="{{ $product->p_name }}" readonly>
                        <label for="product_name">اسم المنتج</label>
                    </div>
                    @endif

                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="AdsName" name="AdsName" placeholder="Advertisement Title" required>
                        <label for="AdsName">اسم الاعلان</label>
                        @error('AdsName')
                            <div class="form-error">
                            <i class="bi bi-exclamation-circle"></i>
                            الرجاء ادخال اسم الاعلان 
                            </div>
                        @enderror
                    </div>

                    <div class="form-floating mb-3">
                        <input type="file" class="form-control" id="AdsImage" name="AdsImage" accept="image/*,video/*">
                        <label for="AdsImage">صورة أو فيديو الإعلان</label>
                        @error('AdsImage')
                            <div class="form-error">
                            <i class="bi bi-exclamation-circle"></i>
                            الرجاء اختيار صورة او فيديو للاعلان 
                            </div>
                        @enderror
                    </div>

                    <div class="form-floating mb-3">
                        <input type="number" class="form-control" id="offerDuration" name="offerDuration" placeholder="Advertisement Title"  min="1" required>
                        <label for="offerDuration">مدة العرض (بالأيام)</label>
                        @error('offerDuration')
                            <div class="form-error">
                            <i class="bi bi-exclamation-circle"></i>
                            الرجاء ادخال مده الاعلان 
                            </div>
                        @enderror
                    </div>

                    {{-- حقل الرابط ونوع الإعلان يتم إظهارهما فقط إذا لم يكن الإعلان خاصاً بمنتج --}}
                    @if(!isset($product))
                        @if(isset($isLinkAd) && $isLinkAd)
                        <div class="form-floating mb-3">
                            <input type="url" class="form-control" id="AdsLink" name="AdsLink" placeholder="Advertisement Title" value="{{ old('AdsLink') }}">
                            <label for="AdsLink">رابط الاعلان</label>
                            @error('AdsLink')
                                <div class="form-error">
                                <i class="bi bi-exclamation-circle"></i>
                                الرجاء ادخال رابط الاعلان 
                                </div>
                            @enderror
                        </div>
                        @endif

                        @if(isset($isLinkAd) && $isLinkAd)
                            <input type="hidden" name="type" value="link">
                        @else
                            <div class="form-floating mb-3">
                                <select class="form-control" id="type" name="type" required>
                                    <option value="" disabled {{ !old('type') ? 'selected' : '' }}>
                                    اختر نوع الإعلان...
                                    </option>
                                    <option value="image" {{ old('type') == 'image' ? 'selected' : '' }}>إعلان خاص</option>
                                    <option value="general" {{ old('type') == 'general' ? 'selected' : '' }}>إعلان عام</option>
                                </select>
                                <label for="type">نوع الإعلان</label>
                            </div>
                        @endif
                    @else
                        {{-- إذا كان إعلان منتج، نحدد نوعه تلقائياً بصيغة مناسبة للخلفية --}}
                        <input type="hidden" name="type" value="image">
                    @endif

                    <div class="radio-buttons-container">
                        <div class="radio-button">
                            <input name="is_active" value="1" id="radio2" class="radio-button__input" type="radio" {{ old('is_active', '1') == "1" ? 'checked' : '' }}>
                            <label for="radio2" class="radio-button__label">
                                <span class="radio-button__custom"></span>
                                مفعل 
                            </label>
                        </div>

                        <div class="radio-button">
                            <input name="is_active" value="0" id="radio1" class="radio-button__input" type="radio" {{ old('is_active') == "0" ? 'checked' : '' }}>
                            <label for="radio1" class="radio-button__label">
                                <span class="radio-button__custom"></span>
                                غير مفعل
                            </label>
                        </div>
                        @error('is_active')
                            <div class="form-error">
                                <i class="bi bi-exclamation-circle"></i>
                                يرجى اختيار حالة الإعلان
                            </div>
                        @enderror
                    </div>

                    <div class="d-grid" style="direction: ltr">
                        <button type="submit" class="btn-submit" id="saveBtn" onclick="activateLoading('dataForm', 'saveBtn');">
                            حفظ الإعلان <i class="bi bi-plus ms-2"></i></button>
                    </div>
                </form>
            </div>
        </div> 
      </div> 
    </div>
</section>
@endsection