@extends('layouts.master')
@section('content')
    <div class="container my-4 " dir="rtl">
        <div class="container">

            {{-- Header Section --}}
            <div class="controls-container">
                <div class="section-ar">

                    <div class="section-title" style="margin-bottom: 0px;">
                        <h3 style="font-size: 20px">قائمة <span class="orange-text">اللوان المنتج</span></h3>
                    </div>
                </div>
                <div class="search-input-container">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" class="search-input" placeholder="Search">
                </div>

                <a href="/permission" class="btn-add-product shadow-sm" style="justify-content: center ;">

                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            </div>

            {{-- Grid Section --}}

        </div>
        <div class="row g-4 px-2">
            @php
                $colors = [
                    (object) [
                        'id' => 101,
                        'name' => 'الاسود',
                        'colorHash' => '#000000',
                        'images' =>
                            'assets/img/photos/a3.jpg,assets/img/photos/product_78.jpg,assets/img/photos/a3.jpg',
                    ],
                    (object) [
                        'id' => 101,
                        'name' => 'الازرق',
                        'colorHash' => '#14eaf9',
                        'images' =>
                            'assets/img/photos/a3.jpg,assets/img/photos/product_78.jpg,assets/img/photos/a3.jpg',
                    ],
                ];
            @endphp

            @forelse($colors as $color)
                <div class="col-12">
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden position-relative bg-white"
         style="transition: all .2s ease;">

        <div class="card-body p-2 p-md-3 d-flex align-items-center justify-content-between">

            {{-- Info Side --}}
            <div class="d-flex align-items-center">

                @php
                    $images = explode(',', $color->images);
                @endphp

                <div class="image-holder ms-2 ms-md-3">
                    <div class="p-1 bg-white rounded-circle border shadow-sm position-relative"
                         style="width:65px; height:65px; overflow:hidden;">

                        @foreach ($images as $index => $img)
                            <img src="{{ asset(trim($img)) }}"
                                 class="rounded-circle color-image"
                                 width="55"
                                 height="55"
                                 style="
                                    object-fit: cover;
                                    position:absolute;
                                    top:5px;
                                    left:5px;
                                    display: {{ $index == 0 ? 'block' : 'none' }};
                                 ">
                        @endforeach

                    </div>
                </div>

                <div class="text-end me-2 me-md-3">

                    <h6 class="mb-1 fw-bold text-dark"
                        style="font-size:14px;">
                        {{ $color->name }}
                    </h6>

                    <span class="d-inline-block rounded-circle"
                          style="
                            width:18px;
                            height:18px;
                            background-color: {{ $color->colorHash ?? '#ccc' }};
                            border:1px solid #ddd;
                          ">
                    </span>

                </div>
            </div>

            {{-- Actions Side --}}
            <div class="d-flex align-items-center gap-2 ms-lg-3">

                <a href="#"
                   class="btn btn-sm btn-light border-0 rounded-3 px-2 px-lg-3 py-1 py-lg-2"
                   style="color:#008870; font-size:13px;">
                    <i class="bi bi-pencil-square ms-1"></i>
                    تعديل
                </a>

                <div class="vr mx-1 d-none d-md-block"
                     style="height:20px; opacity:.1;"></div>

                <a href="#"
                   class="btn btn-sm btn-light text-danger border-0 rounded-3 px-2 px-lg-3 py-1 py-lg-2"
                   style="font-size:13px;">
                    <i class="bi bi-trash ms-1"></i>
                    حذف
                </a>

            </div>

        </div>

        {{-- الخط الجانبي --}}
        <div class="position-absolute start-0 top-0 h-100"
             style="width:4px; opacity:.8; background-color: {{ $color->colorHash ?? '#ccc' }};">
        </div>

    </div>
</div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5 bg-white rounded-4 shadow-sm border border-dashed">
                        <i class="bi bi-inbox text-muted display-4 mb-3"></i>
                        <p class="text-muted fw-bold">لا توجد مقاسات مسجلة حالياً</p>
                        {{-- <a href="#" class="btn btn-sm btn-outline-primary mt-2">ابدأ بإضافة أول مقاس</a> --}}
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    {{-- CSS Inline Improvement (Optional for the hover effect) --}}
@endsection
@section('jsfile')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const containers = document.querySelectorAll('.image-holder');

            containers.forEach(container => {
                const images = container.querySelectorAll('.color-image');
                let index = 0;

                if (images.length > 1) {
                    setInterval(() => {
                        images[index].style.display = "none";
                        index = (index + 1) % images.length;
                        images[index].style.display = "block";
                    }, 2000); // كل ثانيتين
                }
            });
        });
    </script>
@endsection
