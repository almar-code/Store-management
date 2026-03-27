@extends('Layouts.master')
@section('content')
    <!-- Contact Section -->
    <section id="contact" class="contact section">
        <!-- Section Title -->
        <div class="col-lg-8 offset-lg-2 text-center">
            <div class="section-title">
                <h3>Add <span class="orange-text">Color</span></h3>
            </div>
        </div>

        <div class="container my-5" style="direction: rtl; text-align: right">
            <div class="row justify-content-center">

                <div class="col-14 col-md-10 col-lg-8">

                    <div class="form-container">
                        <h3>نموذج إضافة لون للمنتج</h3>
                        <form id="dataForm"
                            action="{{ isset($editColor) ? '/update-color/' . $editColor->color_id : '/add-color/' . $productID }}"
                            method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" readonly required=""
                                    value="{{ $productName ?? '' }}">
                                <label for="productName">اسم المنتج</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="color" class="form-control" id="productColor" name="productColor"
                                    placeholder="productColor" required=""
                                    value="{{ old('productColor', $editColor->color_code ?? '#0000000') }}">
                                <label for="productColor">لون المنتج</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" name="colorName" placeholder="اسم اللون"
                                    value="{{ old('colorName', $editColor->color_name ?? '') }}">
                                <label>اسم اللون</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="file" class="form-control" id="productImages" name="productImages[]"
                                    multiple onchange="previewImage(event)" {{ isset($editColor) ? '' : 'required' }}>
                                <label for="productImages">صور المنتج</label>
                            </div>

                            <!-- From Uiverse.io by vishnupprajapat -->
                            <div class="checkbox-wrapper-46 mb-3">
                                <input type="checkbox" name="keep_old_images" id="cbx-46" class="inp-cbx" />
                                <label for="cbx-46" class="cbx"><span>
                                        <svg viewBox="0 0 12 10" height="10px" width="12px">
                                            <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                        </svg></span> <span>الاحتفاظ بالصور القديمة</span>
                                </label>
                            </div>


                            <div class="d-grid" style="direction: ltr">
                                @if (!isset($editColor))
                                    <button type="button" class="btn-submit" id="saveBtn" onclick="activateLoading('dataForm', 'saveBtn');">

                                        إضافة <i class="bi bi-plus ms-2"></i>

                                    </button>
                                @endif


                                {{-- زر التعديل يظهر فقط في حالة التعديل --}}
                                @if (isset($editColor))
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
