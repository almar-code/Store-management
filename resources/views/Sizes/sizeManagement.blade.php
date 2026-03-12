@extends('layouts.master')
@section('link')
    <link href="{{ asset('assets/css/order.css') }}" rel="stylesheet">
@endsection

@section('content')
    <div class="container my-4 " dir="rtl">
        <div class="container">

            {{-- Header Section --}}
            <div class="controls-container">
                <div class="section-ar">

                    <div class="section-title" style="margin-bottom: 0px;">
                        <h3 style="font-size: 20px">قائمة <span class="orange-text">مقاسات المنتج</span></h3>
                    </div>
                </div>
                <div class="search-input-container">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" class="search-input" placeholder="Search">
                </div>

                <a href="/sizeManagement" class="btn-add-product shadow-sm" style="justify-content: center ;">

                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            </div>

            {{-- Grid Section --}}

        </div>
        <div class="row g-4 px-2">
            @forelse($sizes as $size)
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden position-relative bg-white">

                    <div class="card-body p-2">

                        <div class="d-flex align-items-center justify-content-between">

                            <div class="image-holder ms-3">
                                <div class="p-1 bg-white rounded-circle border shadow-sm">
                                    <img src="{{  asset('storage/uploads/products/' . ($size->product->p_image ?? 'default.png')) }}" class="rounded-circle" width="55" height="55"
                                        style="object-fit: cover;">
                                </div>
                            </div>
                            <div class="text-end flex-grow-1 overflow-hidden">

                                <h6 class="mb-1 fw-bold text-truncate"
                                    style="font-size:13px; white-space:nowrap; color: #000;">
                                    {{ $size->product->p_name ?? '-' }}
                                </h6>

                                <span class="badge rounded-pill bg-light text-secondary border px-2 py-1 fw-normal"
                                    style="font-size:10px;">
                                    <i class="bi bi-rulers me-1 text-primary"></i>
                                    {{ $size->size_name ?? '-' }}
                                </span>

                            </div>
                            <div class="d-flex align-items-center gap-2 ms-lg-3">

                                <a href="edit-size/{{ $size->size_id ?? '-' }}"
                                    class="btn btn-sm btn-lg-lg btn-light border-0 rounded-3 px-2 px-lg-3 py-1 py-lg-2"
                                    style="color:#008870; font-size:13px;">
                                    <i class="bi bi-pencil-square"></i>
                                    تعديل
                                </a>

                                <a href="#" onclick="confirmDelete('/delete-size/{{ $size->size_id }}','حذف المقاس','هل أنت متأكد من حذف هذا المقاس ؟')"
                                    class="btn btn-sm btn-lg-lg btn-light text-danger border-0 rounded-3 px-2 px-lg-3 py-1 py-lg-2"
                                    style="font-size:13px;">
                                    <i class="bi bi-trash"></i>
                                    حذف
                                </a>

                            </div>


                        </div>

                    </div>

                    {{-- الخط الجانبي --}}
                    <div class="position-absolute start-0 top-0 h-100" style="width:4px; background:#008870;"></div>

                </div>

            @empty
                <div class="col-12">
                    <div class="text-center py-5 bg-white rounded-4 shadow-sm border border-dashed">
                        <i class="bi bi-inbox text-muted display-4 mb-3"></i>
                        <p class="text-muted fw-bold">لا توجد مقاسات مسجلة حالياً</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    {{-- CSS Inline Improvement (Optional for the hover effect) --}}
@endsection
