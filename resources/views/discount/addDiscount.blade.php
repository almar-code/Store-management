@extends('Layouts.master')
@section('content')
    <!-- Contact Section -->
    <section id="contact" class="contact section">
        <!-- Section Title -->
        <div class="col-lg-8 offset-lg-2 text-center">
            <div class="section-title">
                @if (isset($editDiscount))
                    <h3>Edit <span class="orange-text">Discount</span></h3>
                @else
                    <h3>Add <span class="orange-text">Discount</span></h3>
                @endif
            </div>
        </div>

        <div class="container my-5" style="direction: rtl; text-align: right">
            <div class="row justify-content-center">

                <div class="col-14 col-md-10 col-lg-8">

                    <div class="form-container">
                        <div style="display: flex;justify-content: space-between">
                            <h3>{{ isset($editDiscount) ? 'نموذج تعديل خصم' : 'نموذج إضافة خصم' }}</h3>
                            <a href="javascript:history.back()">
                                <i class="bi bi-arrow-left fs-4 text-dark"></i>
                            </a>
                        </div>

                        <form id="dataForm"
                            action="{{ isset($editDiscount) ? '/update-discount/' . $editDiscount->discount_id : '/add-discount/' . $productID }}"
                            method="post">
                            @csrf
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="price" name="price" placeholder="" required=""
                                    value="{{ $productPrice }}" readonly>
                                <input type="hidden" name="p_id" value="{{ $productID }}">
                                <label for="price">سعر المنتج</label>
                            </div>

                            <div class="form-floating mb-3">
                                <input type="number" class="form-control" id="discount_perce" name="discount_perce"
                                    placeholder="Subject" required=""
                                    value="{{ old('discount_perce', $editDiscount->discount_perce ?? '') }}">
                                <label for="discount_perce">الخصم</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="number" class="form-control" id="duration" name="duration"
                                    placeholder="Subject" required=""
                                    value="{{ old('duration', $remainingDays ?? '') }}">
                                <label for="duration">{{ isset($editDiscount) ? 'مدة الخصم من اليوم (بالأيام)' : 'مدة الخصم' }}</label>
                            </div>
                            <div class="d-grid" style="direction: ltr">
                                @if (isset($editDiscount))
                                    <button type="button" class="btn-submit" id="saveBtn" onclick="activateLoading('dataForm', 'saveBtn');">
                                        تعديل <i class="bi bi-pencil-square" style=" font-size:13px; margin: 3px"></i>
                                    </button>
                                @else
                                    <button type="button" class="btn-submit" id="saveBtn" onclick="activateLoading('dataForm', 'saveBtn');">إضافة
                                        <i class="bi bi-plus ms-2"></i></button>
                                @endif
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </section><!-- /Contact Section -->
@endsection