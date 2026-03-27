@extends('Layouts.master')
@section('content')
    <div class="container my-4 " dir="rtl">
        <div class="container">

            <div class="controls-container">
                <div class="section-ar">

                    <div class="section-title" style="margin-bottom: 0px;">
                        <h3 style="font-size: 20px">قائمة <span class="orange-text">المنتجات</span></h3>
                    </div>
                </div>
                <div class="search-input-container">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" class="search-input" placeholder="Search" id="sectionSearch">
                </div>

                <a href="/addproduct" class="btn-add-product shadow-sm">
                    إضافة منتج
                    <i class="bi bi-plus-lg ms-2"></i>
                </a>
            </div>
        </div>

        <div class="row g-0">
            @foreach ($products as $product)
                <div class="col-12 col-lg-4 mt-3 card-item">
                    <div class="card product-card text-dark h-100 shadow-sm mx-2">
                        <div class="row g-0 align-items-center px-3 py-2">

                            <div class="col-4 col-md-3">
                                <img src="{{ asset('storage/uploads/products/' . $product->p_image) }}"
                                    class="img-fluid rounded shadow-sm" alt="Product Image"
                                    style="aspect-ratio: 1/1; object-fit: cover; border-radius: 12px !important;">
                            </div>

                            <div class="col-7 col-md-8 px-3 text-end">
                                <div class="d-flex flex-column">
                                    <h5 class="card-title mb-1 fw-bold text-truncate card-title">
                                        {{ $product->p_name ?? '' }}</h5>
                                    <p class="card-text mb-2 small text-muted text-truncate ">
                                        {{ $product->p_description ?? '' }}
                                    </p>
                                    <div class="card-title fw-bold" style="font-size: 1rem;">

                                        @if ($product->discount)
                                            <span style="text-decoration: line-through; color: gray;">
                                                {{(integer)$product->p_price }}
                                            </span>
                                            <span style="color:red; font-weight:bold; padding-right: 10px;">
                                                {{ $product->p_price - $product->discount->discount_perce }}
                                            </span>
                                            <span class="small text-turquoise" style="font-size: 0.8rem;">
                                                ر.س
                                            </span>
                                            <span
                                                style="color:green; font-weight:bold; padding-right: 10px;font-size: 0.8rem;">
                                                خصم {{ round(($product->discount->discount_perce / $product->p_price) * 100) }}%
                                            </span>
                                        @else
                                            {{(integer) $product->p_price }}
                                            <span class="small text-turquoise" style="font-size: 0.8rem;">
                                                ر.س
                                            </span>
                                        @endif

                                    </div>
                                </div>
                            </div>

                            <div class="col-1 text-start align-self-start">
                                <div class="dropdown">
                                    <button class="btn text-dark p-0 border-0 shadow-none" type="button"
                                        data-bs-toggle="dropdown" data-bs-boundary="viewport" aria-expanded="false">
                                        <i class="bi bi-three-dots-vertical fs-4"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-start shadow border-light">
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center justify-content-between py-2 text-end"
                                                href="/edit-product/{{ $product->p_id }}">
                                                <span class="ms-2">تعديل</span>
                                                <i class="bi bi-pencil-square text-turquoise"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center justify-content-between py-2 text-end"
                                                href="/addsize/{{ $product->p_id }}">
                                                <span class="ms-2">اضافة مقاس</span>
                                                <i class="bi bi-plus-circle text-turquoise"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center justify-content-between py-2 text-end"
                                                href="/sizeManagement/{{ $product->p_id }}">
                                                <span class="ms-2">مقاسات المنتج</span>
                                                <i class="bi bi-plus-circle text-turquoise"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center justify-content-between py-2 text-end"
                                                href="/addColor/{{ $product->p_id }}">
                                                <span class="ms-2">اضافة لون</span>
                                                <i class="bi bi-plus-circle text-turquoise"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center justify-content-between py-2 text-end"
                                                href="/colors/{{ $product->p_id }}">
                                                <span class="ms-2">اللوان المنتج</span>
                                                <i class="bi bi-plus-circle text-turquoise"></i>
                                            </a>
                                        </li>
                                        @if (!isset($product->discount))
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center justify-content-between py-2 text-end"
                                                    href="/addDiscount/{{ $product->p_id }}">
                                                    <span class="ms-2">اضافة خصم</span>
                                                    <i class="bi bi-plus-circle text-turquoise"></i>
                                                </a>
                                            </li>
                                        @endif
                                        @if (isset($product->discount))
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center justify-content-between py-2 text-end"
                                                    href="#"
                                                    onclick="confirmDelete('/delete-discount/{{ $product->discount->discount_id }}' ,'حذف الخصم ','هل أنت متأكد من حذف هذا الخصم   ؟')">
                                                    <span class="ms-2">حذف الخصم</span>
                                                    <i class="bi bi-plus-circle text-turquoise"></i>
                                                </a>
                                            </li>
                                        @endif
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center justify-content-between py-2 text-danger font-weight-bold text-end"
                                                href="#"
                                                onclick="confirmDelete('/delete-product/{{ $product->p_id }}','حذف منتج؟','هل أنت متأكد من حذف هذا المنتج؟')">
                                                <span class="ms-2">حذف المنتج</span>
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
