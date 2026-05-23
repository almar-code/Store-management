@extends('Layouts.master')
@section('content')

    <!-- Contact Section -->
    <section id="contact" class="contact section">
        <div class="col-lg-8 offset-lg-2 text-center">
            <div class="section-title">
                <h3>
                    @if (!isset($editVideo)) Add @else Update @endif
                    <span class="orange-text">Video</span>
                </h3>
            </div>
        </div>
       
        <div class="container my-5" style="direction: rtl; text-align: right">
          <div class="row justify-content-center">
            <div class="col-14 col-md-10 col-lg-8">
                
                <div class="form-container">
                    <h3>{{ isset($editVideo) ? 'نموذج تعديل الفيديو' : 'نموذج إضافة فيديو' }}</h3>

                    @if (isset($editVideo))
                        <div class="text-start">
                            <a href="{{ url()->previous() }}">
                                <i class="bi bi-arrow-left fs-4 text-dark"></i>
                            </a>
                        </div>
                    @endif

                    <form id="dataForm" action="{{ url('/add-video/' . ($productID ?? '')) }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- عرض اسم المنتج (قراءة فقط) إذا كان موجوداً -->
                        @if(isset($productName))
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="product_name" value="{{ $productName }}" readonly style="background-color: #e9ecef;">
                            <label for="product_name">المنتج المرتبط بالفيديو</label>
                        </div>
                        @endif

                        <!-- حقل رفع الفيديو -->
                        <div class="form-floating mb-3">
                            <input type="file" class="form-control" id="video_file" name="video_file" 
                                   accept="video/*" {{ isset($editVideo) ? '' : 'required' }}>
                            <label for="video_file">اختر ملف الفيديو</label>
                            
                            @if(isset($editVideo))
                                <div class="mt-2 text-muted small">
                                    <i class="bi bi-info-circle"></i> اترك الحقل فارغاً إذا كنت لا تريد تغيير الفيديو الحالي.
                                </div>
                            @endif

                            @error('video_file')
                                <div class="form-error text-danger mt-1">
                                    <i class="bi bi-exclamation-circle"></i>
                                    الرجاء اختيار ملف فيديو صالح
                                </div>
                            @enderror
                        </div>

                        <!-- الأزرار -->
                        <div class="d-grid" style="direction: ltr">
                            <button type="button" class="btn-submit" id="saveBtn" onclick="activateLoading('dataForm', 'saveBtn');">
                                @if (!isset($editVideo))
                                    إضافة الفيديو <i class="bi bi-plus ms-2"></i>
                                @else
                                    تحديث الفيديو <i class="bi bi-pencil-square ms-2"></i>
                                @endif
                            </button>
                        </div>
                    </form>
                </div>

            </div> 
          </div> 
        </div>
    </section>
@endsection