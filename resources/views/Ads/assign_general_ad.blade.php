@extends('Layouts.master')

@section('link')
    <link href="{{ secure_asset('assets/css/order.css') }}?v={{ time() }}" rel="stylesheet">
    <link href="{{ secure_asset('assets/css/login.css') }}?v={{ time() }}" rel="stylesheet">
@endsection

@section('content')
    <!-- Contact Section -->
    <section id="contact" class="contact section">
        <!-- Section Title -->
        <div class="col-lg-8 offset-lg-2 text-center">
            <div class="section-title">
                <h3>Assign <span class="orange-text">General Ad</span></h3>
            </div>
        </div>

        <div class="container my-5" style="direction: rtl; text-align: right">
            <div class="row justify-content-center">

                <div class="col-14 col-md-10 col-lg-8">

                    <div class="form-container">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                            <h3 class="fs-5 fw-bold m-0">إضافة المنتج إلى عرض عام</h3>
                            <a href="javascript:history.back()">
                                <i class="bi bi-arrow-left fs-4 text-dark"></i>
                            </a>
                        </div>

                        <form id="dataForm" action="{{ url('/assign-general-ad/'.$product->p_id ) }}" method="POST">
                            @csrf   
                            
                            <!-- اسم المنتج (للقراءة فقط) -->
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="product_name" value="{{ $product->p_name }}" readonly>
                                <label for="product_name">اسم المنتج</label>
                            </div>

                            <!-- قائمة منسدلة بالإعلانات العامة المتاحة -->
                            <div class="form-floating mb-3">
                                <select class="form-select" id="ads_id" name="ads_id" required>
                                    <option value="" disabled {{ old('ads_id', $editAd->ads_id ?? '') == '' ? 'selected' : '' }}>
                                         اختر الإعلان العام 
                                    </option>
                                    
                                    @forelse($generalAds as $ad)
                                        <option value="{{ $ad->ads_id }}"
                                            {{ old('ads_id', $editAd->ads_id ?? '') == $ad->ads_id ? 'selected' : '' }}>
                                            {{ $ad->AdsName }} (ينتهي في: {{ is_object($ad->expires_at) ? $ad->expires_at->toDateString() : $ad->expires_at }})
                                        </option>
                                    @empty
                                        <option value="" disabled>لا توجد إعلانات عامة متاحة أو تم إضافة المنتج إليها جميعاً</option>
                                    @endforelse
                                </select>
                                <label for="ads_id">الإعلان العام</label>
                                @error('ads_id')
                                    <div class="form-error">
                                        <i class="bi bi-exclamation-circle"></i> {{ $message }}
                                    </div>
                                @enderror
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