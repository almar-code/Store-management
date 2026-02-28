@extends('layouts.master')
@section('content')
    <div class="main-content-wrapper py-5" style="min-height: 100vh; direction: rtl; background-color: #f8f9fa;">
        <div class="container">

             <div class="aa controls-container" >

                <div class="section-ar">

                    <div class="section-title" style="margin-bottom: 0px;">
                        <h3 style="font-size: 20px">قائمة <span class="orange-text">ألطلبات</span></h3>
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
            <div class="row g-4 px-2">
                @php
                    $categories = [
                        (object) [
                            'id' => 101,
                            'name' => 'مقاسات رسمية',
                            'sizename' => (object) ['name' => '48 Standard'],
                            'image' => 'assets/img/photos/a3.jpg',
                        ],
                        (object) [
                            'id' => 101,
                            'name' => 'مقاسات كاجوال',
                            'sizename' => (object) ['name' => '50 Slim Fit'],
                            'image' => 'assets/img/photos/a3.jpg',
                        ],
                    ];
                @endphp

                @forelse($categories as $category)
                    <div class="col-12 col-xl-11 mx-auto">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden position-relative bg-white" 
                             style="transition: transform 0.2s ease, shadow 0.2s ease;">
                            
                            {{-- Card Body --}}
                            <div class="card-body p-3 d-flex align-items-center justify-content-between  gap-3">
                            {{-- Info Side --}}
                            <div class="d-flex align-items-center">
                                <div class="image-holder ms-3">
                                    <div class="p-1 bg-white rounded-circle border shadow-sm">
                                        <img src="{{ $category->image }}" class="rounded-circle" width="55"
                                            height="55" alt="Abaya Category" style="object-fit: cover;">
                                    </div>
                                </div>
                                <div class="text-end">
                                    {{-- اسم الموديل --}}
                                    <h6 class="mb-1 fw-bold text-dark">{{ $category->name }}</h6>
                                    
                                    {{-- مقاس الطول - بتصميم فاتح وأنيق --}}
                                    <span class="badge rounded-pill bg-light text-secondary border px-3 py-2 fw-normal" style="font-size: 11px;">
                                        <i class="bi bi-rulers me-1 text-primary"></i>
                                        {{ $category->sizename->name ?? '-' }}
                                    </span>

                                
                                </div>
                            </div>

                                {{-- Actions Side --}}
                                <div class="d-flex align-items-center gap-2">
                                    <a href="#" class="btn btn-sm btn-light  border-0 px-1 py-1 px-md-3 py-md-2 me-1 me-md-2 rounded-3 hover-shadow transition d-flex align-items-center gap-1" 
                                    style=" color:#008870; font-size: 11px ">
                                        <i class="bi bi-pencil-square ms-1"></i> تعديل
                                    </a>
                                    <div class="vr mx-1 d-none d-md-block" style="height: 20px; opacity: 0.1;"></div>
                                    <a href="#" class="btn btn-sm btn-light text-danger border-0 px-1 py-1 px-md-3 py-md-2 me-1 me-md-2 rounded-3 hover-shadow transition d-flex align-items-center gap-1"
                                     style="font-size: 11px">
                                        <i class="bi bi-trash ms-1"></i> حذف
                                    </a>
                                </div>

                            </div>

                            {{-- Subtle Hover Effect Decoration --}}
                            <div class="position-absolute start-0 top-0 h-100 " style="width: 4px; opacity: 0.8; background-color:#008870 "></div>
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
    </div>

    <style>
     .controls-container {
    width: 90%;
}

@media (max-width: 767px) {
    .controls-container {
        width: 100%;
    }
    
     
    
}

    </style>
    
@endsection