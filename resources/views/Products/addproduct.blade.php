@extends('layouts.master')
@section('content')
    <!-- Contact Section -->
    <section id="contact" class="contact section">
        <!-- Section Title -->
        <div class="col-lg-8 offset-lg-2 text-center">
            <div class="section-title">
                <h3> @if (!isset($editProduct))
                        Add 
                    @endif
                    @if (isset($editProduct))
                        update 
                    @endif <span class="orange-text">Product</span></h3>
            </div>
        </div>

        <div class="container my-5" style="direction: rtl; text-align: right">
            <div class="row justify-content-center">

                <div class="col-14 col-md-10 col-lg-8">

                    <div class="form-container">
                        <h3> {{ isset($editProduct) ? 'نموذج تعديل منتج' : 'نموذج إضافة منتج' }}</h3>
                        @if (isset($editProduct))
                            <div class="text-start">
                                <a href="{{ url()->previous() }}">
                                    <i class="bi bi-arrow-left fs-4 text-dark"></i>
                                </a>
                            </div>
                        @endif
                        <form id="dataForm" action="{{ isset($editProduct) ? '/update-product/' . $editProduct->p_id : '/add-product' }}"
                            method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="productName" name="productName"
                                    placeholder="Full Name"
                                    value="{{ old('productName', $editProduct->p_name ?? '') }}">
                                <label for="productName">اسم المنتج</label>
                            </div>
                            <div class="d-flex align-items-end gap-3">

                                <div class="form-floating mb-3 flex-grow-1">
                                    <input type="file" class="form-control" id="productImages" name="productImages[]"
                                        multiple onchange="previewImage(event)" {{ isset($editProduct) ? '' : 'required' }}>

                                    <label for="productImages">صور المنتج</label>

                                    @error('productImages.*')
                                        <div class="form-error">
                                            <i class="bi bi-exclamation-circle"></i>
                                            الرجاء ادخال صور فقط
                                        </div>
                                    @enderror
                                </div>

                                @isset($editProduct)
                                    <div class="image-preview mb-3">
                                        <img id="imagePreview"
                                            src="{{ asset('storage/uploads/products/' . $editProduct->p_image) }}"
                                            class="rounded" width="60" height="60" style="object-fit: cover;">
                                    </div>
                                @else
                                    <div class="image-preview mb-3">
                                        <img id="imagePreview" class="rounded" width="60" height="60"
                                            style="object-fit: cover; display:none;">
                                    </div>
                                @endisset

                            </div>

                            <div class="form-floating mb-3">
                                <input type="number" class="form-control" id="productPrice" name="productPrice"
                                    placeholder="Subject" lang="en" inputmode="numeric"
                                    value="{{ old('productPrice', $editProduct->p_price ?? '') }}">
                                <label for="productPrice">سعر المنتج</label>
                                @error('productPrice')
                                    <div class="form-error">

                                        <i class="bi bi-exclamation-circle"></i>

                                        الرجاء ادخال قيمة صحيحة

                                    </div>
                                @enderror
                            </div>

                            <div class="form-floating mb-3">
                                <textarea class="form-control" id="productDescription" name="productDescription" rows="3"
                                    placeholder="productColor" required="">{{ old('productDescription', $editProduct->p_description ?? '') }}</textarea>
                                <label for="productDescription">وصف المنتج</label>
                            </div>

                            <div class="form-floating mb-3" {{ isset($editProduct) ? 'hidden' : '' }}>
                                <input type="color" class="form-control" id="productColor" name="productColor"
                                    placeholder="productColor" required="" value="{{ old('productColor') }}">
                                <label for="productColor">لون المنتج</label>
                            </div>
                            <div class="form-floating mb-3" {{ isset($editProduct) ? 'hidden' : '' }}>
                                <input type="text" class="form-control" name="colorName" placeholder="اسم اللون"
                                    value="{{ old('colorName') }}">
                                <label>اسم اللون</label>
                            </div>
                            <div class="form-floating mb-3 {{ isset($editProduct) ? 'hidden' : '' }}"
                                {{ isset($editProduct) ? 'hidden' : '' }}>
                                <input type="text" class="form-control" id="productSize" name="productSize"
                                    placeholder="Full Name" value="{{ old('productSize') }}">
                                <label for="productSize">مقاس المنتج</label>
                            </div>

                            <div class="form-floating mb-3">
                                <select class="form-control" id="productSubcategory" name="productSubcategory" required>
                                    <option value="" disabled
                                        {{ !isset($editProduct) && !old('productSubcategory') ? 'selected' : '' }}>
                                        اختر الفئة...
                                    </option>

                                    @foreach ($subCategories as $subCategory)
                                        <option value="{{ $subCategory->subcat_id }}"
                                            {{ old('productSubcategory', $editProduct->subcat_id ?? '') == $subCategory->subcat_id ? 'selected' : '' }}>
                                            {{ $subCategory->subcat_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="productSubcategory">فئة المنتج</label>
                            </div>

                            <div class="d-grid" style="direction: ltr">
                                @if (!isset($editProduct))
                                    <button type="button" class="btn-submit" id="saveBtn" onclick="activateLoading('dataForm', 'saveBtn');">

                                        إضافة <i class="bi bi-plus ms-2"></i>

                                    </button>
                                @endif


                                {{-- زر التعديل يظهر فقط في حالة التعديل --}}
                                @if (isset($editProduct))
                                    <button type="button" class="btn-submit" id="saveBtn" onclick="activateLoading('dataForm', 'saveBtn');">

                                        تعديل <i class="bi bi-pencil-square" style=" font-size:13px; margin: 3px"></i>

                                    </button>
                                @endif
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </section><!-- /Contact Section -->
@endsection
