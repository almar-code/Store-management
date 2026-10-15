@extends('Layouts.master')

@section('link')
    <link href="{{ secure_asset('assets/css/order.css') }}?v={{ time() }}" rel="stylesheet">
    <link href="{{ secure_asset('assets/css/login.css') }}?v={{ time() }}" rel="stylesheet">
@endsection

@section('content')
<div class="container my-4 px-2 px-md-2" dir="rtl">
    <div class="container">
        <!-- رأس الصفحة -->
            <div class="controls-container">
                <div class="section-ar">

                    <div class="section-title" style="margin-bottom: 0px;">
                        <h3 style="font-size: 20px">قائمة <span class="orange-text">المنتجات المرتبطه بإعلان</span></h3>
                    </div>
                </div>
                <div class="search-input-container">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" class="search-input" placeholder="Search" id="sectionSearch">
                </div>

                 <a href="/ads" class="btn-add-product shadow-sm">
                    <i class="bi bi-arrow-right ms-1"></i>
                     العودة للقائمة
                </a>
            </div>

        <!-- جدول المنتجات -->
        <div class="table-container">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>متسلسل</th>
                        <th>اسم المنتج</th>
                        <th>السعر</th>
                        <th>صورة المنتج</th>
                        <th>العمليات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                    <tr class="card-item">
                        <td class="fs-6 fs-md-5">{{ $loop->iteration }}</td>
                        <td class="fw-bold lh-1 fs-md-5">{{ $product->p_name }}</td>
                        <td class="text-muted lh-1 fs-md-5">{{ number_format($product->p_price, 2) }} ر.ي</td>
                        <td class="card-text">
                            @if(!empty($product->p_image))
                                <img src="{{ asset('storage/uploads/products/' . $product->p_image) }}" class="rounded-circle" width="50" height="50" style="object-fit: cover;">
                            @else
                                <span class="badge bg-secondary">بدون صورة</span>
                            @endif
                        </td>
                        <td>
                            <!-- زر إزالة المنتج من العرض العام -->
                            <a class="text-danger" href="#" 
                                onclick="confirmDelete('/ads/{{ $ad->ads_id }}/products/{{ $product->p_id }}/remove', 'إزالة المنتج', 'هل أنت متأكد من إزالة هذا المنتج من العرض العام؟')"
                                title="إزالة من العرض">
                                <i class="bi bi-trash fs-5"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">
                            لا توجد منتجات مرتبطة بهذا الإعلان العام حالياً.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection