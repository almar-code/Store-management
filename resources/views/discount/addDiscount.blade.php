@extends('Layouts.master')
@section('content')
    <!-- Contact Section -->
    <section id="contact" class="contact section">
        <!-- Section Title -->
        <div class="col-lg-8 offset-lg-2 text-center">
            <div class="section-title">
                <h3>Add <span class="orange-text">Discount</span></h3>
            </div>
        </div>

        <div class="container my-5" style="direction: rtl; text-align: right">
            <div class="row justify-content-center">

                <div class="col-14 col-md-10 col-lg-8">

                    <div class="form-container">
                        <div style="display: flex;justify-content: space-between">
                            <h3>نموذج إضافة خصم</h3>
                            <a href="javascript:history.back()">
                                <i class="bi bi-arrow-left fs-4 text-dark"></i>
                            </a>
                        </div>

                        <form id="dataForm" action="{{'/add-discount/'.$productID }}" method="post">
                            @csrf
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="price" name="price" placeholder="" required=""
                                    value="{{ $productPrice }}" readonly>
                                    <input type="hidden" name="p_id" value="{{ $productID }}">
                                <label for="price">سعر المنتج</label>
                            </div>

                            <div class="form-floating mb-3">
                                <input type="number" class="form-control" id="discount_perce" name="discount_perce"
                                    placeholder="Subject" required="">
                                <label for="discount_perce">الخصم</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="number" class="form-control" id="duration" name="duration"
                                    placeholder="Subject" required="">
                                <label for="duration">مدة الخصم</label>
                            </div>
                            <div class="d-grid" style="direction: ltr">
                                <button type="button" class="btn-submit" id="saveBtn" onclick="activateLoading('dataForm', 'saveBtn');">إضافة 
                                    <i class="bi bi-plus ms-2"></i></button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </section><!-- /Contact Section -->
@endsection
