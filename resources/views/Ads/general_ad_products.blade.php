@extends('Layouts.master')

  @section('link')
    <link href="{{ secure_asset('assets/css/order.css') }}?v={{ time() }}" rel="stylesheet">
    <link href="{{ asset('assets/css/order.css') }}?v={{ time() }}" rel="stylesheet">
    <link href="{{ secure_asset('assets/css/login.css') }}?v={{ time() }}" rel="stylesheet">
    <link href="{{ asset('assets/css/login.css') }}?v={{ time() }}" rel="stylesheet">
@endsection

@section('content')
<div class="container my-4 px-2 px-md-2 at-page" dir="rtl">
    <div class="container">
        {{-- رأس الصفحة --}}
        <div class="controls-container">
            <div class="section-ar">
                <div class="section-title" style="margin-bottom: 0px;">
                    <h3 style="font-size: 20px">
                        منتجات <span class="orange-text">{{ $ad->AdsName }}</span>
                        <span class="at-count">{{ $products->count() }}</span>
                    </h3>
                </div>
            </div>

            <div class="search-input-container">
                <i class="bi bi-search search-icon"></i>
                <input type="text" class="search-input" placeholder="ابحث عن منتج..." id="sectionSearch">
            </div>

            <a href="/ads" class="at-btn" title="العودة لقائمة الإعلانات">
                <span class="at-btn-text">قائمة الإعلانات</span>
                <i class="bi bi-arrow-left"></i>
            </a>
        </div>

        {{-- جدول المنتجات --}}
        <div class="table-container table-responsive">
            <table class="table align-middle at-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>صورة المنتج</th>
                        <th>اسم المنتج</th>
                        <th>السعر</th>
                        <th class="text-center">إزالة</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                    <tr class="card-item">
                        <td><span class="at-num">{{ $loop->iteration }}</span></td>
                        <td>
                            @if(!empty($product->p_image))
                                <img src="{{ asset('storage/uploads/products/' . $product->p_image) }}" class="at-thumb-sq" alt="" loading="lazy">
                            @else
                                <span class="at-noimg"><i class="bi bi-image"></i></span>
                            @endif
                        </td>
                        <td class="fw-bold lh-1 fs-md-5 card-title">{{ $product->p_name }}</td>
                        <td class="at-muted fw-bold at-nowrap">{{ number_format($product->p_price, 2) }} ر.ي</td>
                        <td class="text-center">
                            {{-- إزالة المنتج من العرض العام --}}
                            <a class="at-remove" href="#" title="إزالة من العرض"
                               onclick="confirmDelete('/ads/{{ $ad->ads_id }}/products/{{ $product->p_id }}/remove', 'إزالة المنتج', 'هل أنت متأكد من إزالة هذا المنتج من العرض العام؟')">
                                <i class="bi bi-trash3"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="at-empty">
                            <i class="bi bi-bag-x"></i>
                            لا توجد منتجات مرتبطة بهذا الإعلان العام حالياً
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
