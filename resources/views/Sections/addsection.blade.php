@extends('Layouts.master')
@section('content')
    <!-- Contact Section -->
    <section id="contact" class="contact section">
        <!-- Section Title -->
        <div class="col-lg-8 offset-lg-2 text-center">
            <div class="section-title">
                <h3> @if (!isset($editSection))
                        Add 
                    @endif
                    @if (isset($editSection))
                        update 
                    @endif <span class="orange-text">sections</span></h3>
            </div>
        </div>
        <div class="container my-5" style="direction: rtl; text-align: right">
            <div class="row justify-content-center">

                <div class="col-14 col-md-10 col-lg-8">

                    <div class="form-container">
                        <h3> {{ isset($editSection) ? 'نموذج تعديل قسم' : 'نموذج إضافة قسم ' }}</h3> 
                        @if (isset($editSection))
                            <div class="text-start">
                                <a href="{{ url()->previous() }}">
                                    <i class="bi bi-arrow-left fs-4 text-dark"></i>
                                </a>
                            </div>
                        @endif

                        <form id="dataForm" {{-- إذا كان هناك قسم للتعديل يتغير الرابط --}}
                            action="{{ isset($editSection) ? '/update-section/' . $editSection->cat_id : '/add-section' }}"
                            method="POST">

                            @csrf


                            <div class="form-floating mb-3">

                                <input type="text" class="form-control @error('sectionName') is-invalid @enderror"
                                    id="sectionName" name="sectionName" {{-- إذا كنا في التعديل نضع القيمة --}}
                                    value="{{ $editSection->cat_name ?? '' }}" placeholder="اسم القسم">

                                <label for="sectionName">اسم القسم</label>


                                @error('sectionName')
                                    <div class="form-error">

                                        <i class="bi bi-exclamation-circle"></i>

                                        الرجاء ادخال اسم القسم

                                    </div>
                                @enderror

                            </div>


                            <div class="d-grid" style="direction: ltr">


                                {{-- زر الإضافة يظهر فقط إذا لم يكن هناك تعديل --}}
                                @if (!isset($editSection))
                                    <button type="button" class="btn-submit" id="saveBtn" onclick="activateLoading('dataForm', 'saveBtn');">

                                        إضافة <i class="bi bi-plus ms-2"></i>

                                    </button>
                                @endif


                                {{-- زر التعديل يظهر فقط في حالة التعديل --}}
                                @if (isset($editSection))
                                    <button type="button" class="btn-submit" id="saveBtn" onclick="activateLoading('dataForm', 'saveBtn');">

                                        تعديل <i class="bi bi-pencil-square" style=" font-size:13px; margin: 3px"></i>

                                    </button>
                                @endif


                            </div>

                        </form>
                    </div>
                </div>
    </section><!-- /Contact Section -->
@endsection
