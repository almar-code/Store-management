@extends('Layouts.master')

@section('link')

<style>

    .performance-page {
        padding: 25px;
        direction: rtl;
    }

    /* العنوان */
    .performance-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        gap: 15px;
        flex-wrap: wrap;
    }

    .performance-title {
        margin: 0;
        font-size: 25px;
        font-weight: 700;
        color: #222;
    }

    .performance-description {
        margin-top: 7px;
        color: #777;
        font-size: 14px;
    }

    /* الإحصائيات */
    .performance-summary {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 18px;
        margin-bottom: 30px;
    }

    .summary-card {
        background: #fff;
        border-radius: 14px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 15px;
        border: 1px solid #eee;
        box-shadow: 0 3px 12px rgba(0,0,0,0.05);
    }

    .summary-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }

    .summary-best .summary-icon {
        background: #e8f8ef;
        color: #20a464;
    }

    .summary-normal .summary-icon {
        background: #fff5df;
        color: #d99a00;
    }

    .summary-slow .summary-icon {
        background: #fdecec;
        color: #d9534f;
    }

    .summary-info h4 {
        margin: 0 0 5px;
        font-size: 14px;
        color: #777;
        font-weight: 500;
    }

    .summary-info span {
        font-size: 25px;
        font-weight: 700;
        color: #222;
    }

    /* عنوان القائمة */
    .products-section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 18px;
    }

    .products-section-header h3 {
        margin: 0;
        font-size: 19px;
        font-weight: 700;
        color: #222;
    }

    .products-count {
        background: #f1f1f1;
        padding: 6px 12px;
        border-radius: 20px;
        color: #666;
        font-size: 13px;
    }

    /* شبكة المنتجات */
    .products-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 22px;
    }

    /* بطاقة المنتج */
    .product-performance-card {
        background: #fff;
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid #eee;
        box-shadow: 0 4px 15px rgba(0,0,0,0.06);
        transition: 0.25s ease;
    }

    .product-performance-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.10);
    }

    /* صورة المنتج */
    .product-image-container {
        position: relative;
        width: 100%;
        height: 270px;
        background: #f6f6f6;
        overflow: hidden;
    }

    .product-image-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .product-image-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #aaa;
        font-size: 55px;
    }

    /* شارة الأداء */
    .performance-badge {
        position: absolute;
        top: 14px;
        right: 14px;
        padding: 7px 13px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        z-index: 2;
    }

    .badge-best {
        background: #e8f8ef;
        color: #16834d;
    }

    .badge-normal {
        background: #fff3d6;
        color: #a87500;
    }

    .badge-slow {
        background: #fde8e8;
        color: #c0392b;
    }

    /* معلومات المنتج */
    .product-info {
        padding: 18px;
    }

    .product-id {
        color: #999;
        font-size: 12px;
        margin-bottom: 6px;
    }

    .product-name {
        margin: 0 0 12px;
        font-size: 17px;
        font-weight: 700;
        color: #222;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .product-details {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top: 1px solid #eee;
        padding-top: 13px;
    }

    .product-price {
        font-size: 16px;
        font-weight: 700;
        color: #222;
    }

    .product-performance-text {
        font-size: 13px;
        font-weight: 600;
    }

    .text-best {
        color: #16834d;
    }

    .text-normal {
        color: #a87500;
    }

    .text-slow {
        color: #c0392b;
    }

    /* حالة عدم وجود منتجات */
    .empty-products {
        background: #fff;
        border: 1px dashed #ddd;
        border-radius: 15px;
        padding: 60px 20px;
        text-align: center;
        color: #888;
    }

    .empty-products i {
        font-size: 50px;
        margin-bottom: 15px;
        display: block;
    }

    /* القفل */
    .locked-wrapper {
        position: relative;
    }

    .lock-overlay {
        position: absolute;
        inset: 0;
        z-index: 20;
        background: rgba(255,255,255,0.82);
        backdrop-filter: blur(3px);
        display: flex;
        align-items: flex-start;
        justify-content: center;
        padding-top: 180px;
        border-radius: 15px;
    }

    .lock-card {
        background: #fff;
        padding: 30px;
        border-radius: 16px;
        text-align: center;
        box-shadow: 0 8px 30px rgba(0,0,0,0.12);
        max-width: 400px;
    }

    .lock-icon {
        width: 65px;
        height: 65px;
        border-radius: 50%;
        background: #f3f3f3;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
        font-size: 27px;
        color: #777;
    }

    .lock-card h4 {
        margin-bottom: 8px;
        font-weight: 700;
    }

    .lock-card p {
        color: #777;
        font-size: 14px;
        margin: 0;
    }

    /* Responsive */
    @media (max-width: 1100px) {

        .products-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .performance-summary {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 750px) {

        .performance-page {
            padding: 15px;
        }

        .products-grid {
            grid-template-columns: 1fr;
        }

        .performance-summary {
            grid-template-columns: 1fr;
        }

        .product-image-container {
            height: 300px;
        }
    }

</style>

@endsection


@section('content')

@php

    /*
    |--------------------------------------------------------------------------
    | حساب عدد المنتجات حسب التصنيف
    |--------------------------------------------------------------------------
    */

    $bestSellerCount = $results->where('performance', 'Best Seller')->count();

    $normalCount = $results->where('performance', 'Normal')->count();

    $slowMovingCount = $results->where('performance', 'Slow Moving')->count();

@endphp


<div class="performance-page">

    {{-- ========================================================= --}}
    {{-- العنوان --}}
    {{-- ========================================================= --}}

    <div class="performance-header">

        <div>

            <h2 class="performance-title">
                تحليل أداء المنتجات
            </h2>

            <p class="performance-description">
                تصنيف المنتجات حسب مستوى الأداء اعتمادًا على بيانات المنتجات وتفاعل العملاء.
            </p>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- ملخص الأداء --}}
    {{-- ========================================================= --}}

    <div class="performance-summary">


        {{-- الأكثر مبيعًا --}}

        <div class="summary-card summary-best">

            <div class="summary-icon">
                <i class="bi bi-graph-up-arrow"></i>
            </div>

            <div class="summary-info">

                <h4>
                    الأكثر مبيعًا
                </h4>

                <span>
                    {{ $bestSellerCount }}
                </span>

            </div>

        </div>


        {{-- الأداء الطبيعي --}}

        <div class="summary-card summary-normal">

            <div class="summary-icon">
                <i class="bi bi-bar-chart"></i>
            </div>

            <div class="summary-info">

                <h4>
                    أداء طبيعي
                </h4>

                <span>
                    {{ $normalCount }}
                </span>

            </div>

        </div>


        {{-- بطيء الحركة --}}

        <div class="summary-card summary-slow">

            <div class="summary-icon">
                <i class="bi bi-graph-down-arrow"></i>
            </div>

            <div class="summary-info">

                <h4>
                    بطيء الحركة
                </h4>

                <span>
                    {{ $slowMovingCount }}
                </span>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- قائمة المنتجات --}}
    {{-- ========================================================= --}}

    <div class="products-section-header">

        <h3>
            نتائج تحليل المنتجات
        </h3>

        <span class="products-count">

            {{ $results->count() }}

            منتج

        </span>

    </div>


    {{-- ========================================================= --}}
    {{-- المنتجات --}}
    {{-- ========================================================= --}}

    @if($results->count() > 0)

        <div class="products-grid">


            @foreach($results as $result)

                @php

                    $product = $result['product'];

                    $performance = $result['performance'];

                    /*
                    |--------------------------------------------------------------------------
                    | تحديد اسم وتصميم حالة الأداء
                    |--------------------------------------------------------------------------
                    */

                    if ($performance === 'Best Seller') {

                        $performanceName = 'الأكثر مبيعًا';

                        $badgeClass = 'badge-best';

                        $textClass = 'text-best';

                        $icon = 'bi-graph-up-arrow';

                    } elseif ($performance === 'Normal') {

                        $performanceName = 'أداء طبيعي';

                        $badgeClass = 'badge-normal';

                        $textClass = 'text-normal';

                        $icon = 'bi-bar-chart';

                    } else {

                        $performanceName = 'بطيء الحركة';

                        $badgeClass = 'badge-slow';

                        $textClass = 'text-slow';

                        $icon = 'bi-graph-down-arrow';

                    }

                @endphp


                <div class="product-performance-card">


                    {{-- صورة المنتج --}}

                    <div class="product-image-container">


                        {{-- حالة الأداء --}}

                        <span class="performance-badge {{ $badgeClass }}">

                            <i class="bi {{ $icon }}"></i>

                            {{ $performanceName }}

                        </span>


                        @if($product && $product->p_image)

                            <img
                                src="{{ asset('storage/uploads/products/' . $product->p_image) }}"
                                alt="{{ $product->p_name }}"
                            >

                        @else

                            <div class="product-image-placeholder">

                                <i class="bi bi-image"></i>

                            </div>

                        @endif


                    </div>


                    {{-- بيانات المنتج --}}

                    <div class="product-info">


                        {{-- رقم المنتج --}}

                        <div class="product-id">

                            رقم المنتج:

                            #{{ $result['product_id'] }}

                        </div>


                        {{-- اسم المنتج --}}

                        <h4 class="product-name"
                            title="{{ $product->p_name ?? '' }}">

                            {{ $product->p_name ?? 'منتج بدون اسم' }}

                        </h4>


                        {{-- السعر والحالة --}}

                        <div class="product-details">


                            <span class="product-price">

                                {{ number_format($product->p_price ?? 0, 2) }}

                            </span>


                            <span class="product-performance-text {{ $textClass }}">

                                {{ $performanceName }}

                            </span>


                        </div>

                    </div>


                </div>


            @endforeach


        </div>

    @else


        {{-- لا توجد منتجات --}}

        <div class="empty-products">

            <i class="bi bi-box-seam"></i>

            <h4>
                لا توجد منتجات لعرضها
            </h4>

            <p>
                لا توجد منتجات ضمن الفترة المحددة للتحليل.
            </p>

        </div>

    @endif


</div>

@endsection


@section('jsfile')

@endsection