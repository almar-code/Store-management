@extends('layouts.master')
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
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                            <h3>نموذج إضافة اعلانات </h3>
                    </div>

                    <form id="dataForm" action="{{ '/add-ads' }}" method="post" enctype="multipart/form-data">
                     @csrf   
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="AdsName" name="AdsName"  placeholder="Advertisement Title" >
                            <label for="AdsName">اسم الاعلان</label>
                            @error('AdsName')
                                <div class="form-error">
                                <i class="bi bi-exclamation-circle"></i>
                                الرجاء ادخال اسم الاعلان 
                                </div>
                            @enderror
                        </div>
                       <div class="form-floating mb-3">
                            <input type="file" 
                                class="form-control" 
                                id="AdsImage" 
                                name="AdsImage" 
                                placeholder="Subject" 
                                accept="image/*,video/*"
                                >
                            <label for="AdsImage">صورة أو فيديو المنتج</label>
                        @error('AdsImage')
                            <div class="form-error">
                            <i class="bi bi-exclamation-circle"></i>
                            الرجاء اختيار صورة او فيديو  للاعلان 
                            </div>
                        @enderror
                        </div>


                        <div class="form-floating mb-3">
                            <input type="number" class="form-control" id="offerDuration" name="offerDuration"  placeholder="Advertisement Title" >
                            <label for="offerDuration">مدة العرض </label>
                        @error('offerDuration')
                            <div class="form-error">
                            <i class="bi bi-exclamation-circle"></i>
                            الرجاء ادخال مده الاعلان 
                            </div>
                        @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <input type="url" class="form-control" id="AdsLink" name="AdsLink"  placeholder="Advertisement Title" >
                            <label for="AdsLink">رابط الاعلان </label>
                        @error('AdsLink')
                            <div class="form-error">
                            <i class="bi bi-exclamation-circle"></i>
                            الرجاء ادخال رابط الاعلان 
                            </div>
                        @enderror
                        </div>
                        <div class="radio-buttons-container">
                            <div class="radio-button">
                                <input name="is_active" value="1" id="radio2"
                                    class="radio-button__input" type="radio"
                                    {{ old('is_active') == "1" ? 'checked' : '' }}>
                                <label for="radio2" class="radio-button__label">
                                    <span class="radio-button__custom"></span>
                                    مفعل 
                                </label>
                            </div>

                            <div class="radio-button">
                                <input name="is_active" value="0" id="radio1"
                                    class="radio-button__input" type="radio"
                                    {{ old('is_active') == "0" ? 'checked' : '' }}>
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
                                إضافة <i class="bi bi-plus ms-2"></i></button>
                        </div>
                    </form>
                </div>

        </div> </div> </div>
    </section>
@endsection

