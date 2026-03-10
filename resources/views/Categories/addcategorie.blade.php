@extends('layouts.master')
@section('content')

    <!-- Contact Section -->
    <section id="contact" class="contact section">
        <!-- Section Title -->
        <div class="col-lg-8 offset-lg-2 text-center">
            <div class="section-title">
                <h3>
                    @if (!isset($editCategory))
                        Add 
                    @endif
                    @if (isset($editCategory))
                        update 
                    @endif
                    <span class="orange-text">categorie</span></h3>
            </div>
        </div>
       
        <div class="container my-5" style="direction: rtl; text-align: right">
          <div class="row justify-content-center">
            
            <div class="col-14 col-md-10 col-lg-8">
                
                <div class="form-container">
                    @if (!isset($editCategory))
                        <h3>نموذج إضافة الفئات</h3>
                    @endif
                    @if (isset($editCategory))
                        <h3>نموذج تعديل الفئات</h3>
                    @endif
                    @if (isset($editCategory))
                            <div class="text-start">
                                <a href="{{ url()->previous() }}">
                                    <i class="bi bi-arrow-left fs-4 text-dark"></i>
                                </a>
                            </div>
                        @endif
                    <form id="dataForm" action="{{ isset($editCategory) ? '/update-categorie/' . $editCategory->subcat_id : '/add-categorie' }}" 
                        method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="subcat_name" name="subcat_name" placeholder="Name"
                            value="{{ $editCategory->subcat_name ?? old('subcat_name')}}" 
                            >
                            <label for="subcat_name" >اسم الفئه</label>
                            @error('subcat_name')
                                    <div class="form-error">
                                        <i class="bi bi-exclamation-circle"></i>
                                        الرجاء ادخال اسم الفئه
                                    </div>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <input type="file" class="form-control" id="subcat_image" name="subcat_image" onchange="previewImage(event)">
                            <label for="subcat_image" class="form-label">صورة الفئه</label>
                        @if (isset($editCategory))
                            <div class="mt-2">
                                <img id="imagePreview"
                                    src="{{  asset('storage/uploads/subcategory/'.$editCategory->subcat_image) ?? '' }}"
                                    class="rounded-circle"
                                    width="60"
                                    height="60"
                                    style="object-fit: cover;" >
                            </div>
                            @endif

                            @error('subcat_image')
                                    <div class="form-error">
                                        <i class="bi bi-exclamation-circle"></i>
                                        الرجاء اختيار صورة الفئه 
                                    </div>
                            @enderror
                        </div>

                        <div class="form-floating mb-3">
                            <select class="form-select" id="cat_id" name="cat_id"> 
                                
                                <option value="" disabled {{ old('cat_id', $editCategory->cat_id ?? '') == '' ? 'selected' : '' }}>
                                    اختر القسم...
                                </option>
                                
                                @foreach($categories as $category)
                                       <option value="{{ $category->cat_id }}"
                                        {{ old('cat_id', $editCategory->cat_id ?? '') == $category->cat_id ? 'selected' : '' }}>
                                        {{ $category->cat_name }}
                                       </option>
                                    @endforeach
                                
                                   
                            </select>
                            <label for="cat_id">القسم</label>
                            @error('cat_id')
                                <div class="form-error">
                                    <i class="bi bi-exclamation-circle"></i> الرجاء اختيار قسم الفئة
                                </div>
                            @enderror
                        </div>

                        <div class="d-grid" style="direction: ltr">
                            @if (!isset($editCategory))
                                    <button type="submit" class="btn-submit" id="saveBtn">
                                        إضافة <i class="bi bi-plus ms-2"></i>
                                    </button>
                            @endif

                            @if (isset($editCategory))
                                    <button type="submit" class="btn-submit" id="saveBtn">
                                        تعديل <i class="bi bi-pencil-square" style=" font-size:13px; margin: 3px"></i>
                                    </button>
                            @endif
                        </div>
                    </form>
                </div>

        </div> </div> </div>
    </section><!-- /Contact Section -->
@endsection

