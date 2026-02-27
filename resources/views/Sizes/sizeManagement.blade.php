@extends('layouts.master')
@section('content')
    <div class="main-content-wrapper py-5" style="min-height: 100vh; direction: rtl; background-color: #f8f9fa;">
        <div class="container">

         {{-- Header Section --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-5 gap-3">

                <div class="section-ar">
                    <div class="section-title">
                        <h3 class="fw-bold m-0 fs-5 me-3 text-nowrap"
                            style="color: #2d3436; letter-spacing: -0.5px;">
                            إدارة <span class="text-success">المقاسات</span>
                        </h3>
                    </div>
                </div>

                <div class="d-flex flex-column flex-md-row gap-3 align-items-center w-100 w-md-auto">

                    {{-- Search Input --}}
                    <div class="position-relative">
                        <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                        <input
                            type="text"
                            class="form-control border-0 shadow-sm ps-5 py-2 px-4 rounded-3"
                            placeholder="بحث عن مقاس..."
                            style="min-width: 280px;">
                    </div>

                    {{-- Add Button --}}
                    <a href="#"
                    class="btn shadow-sm px-4 py-2 rounded-3 d-flex align-items-center fw-bold"
                    style="background-color: #008870; border: none; color: #fff;">
                        <i class="bi bi-plus-lg me-2"></i>
                        <span>إضافة مقاس جديد</span>
                    </a>

                </div>
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
                            <div class="card-body p-3 d-flex align-items-center justify-content-between flex-wrap gap-3">
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
                                    <a href="#" class="btn btn-sm btn-light  border-0 px-3 py-2 rounded-3 hover-shadow transition" style="color:#008870 ">
                                        <i class="bi bi-pencil-square ms-1"></i> تعديل
                                    </a>
                                    <div class="vr mx-1 d-none d-md-block" style="height: 20px; opacity: 0.1;"></div>
                                    <a href="#" class="btn btn-sm btn-light text-danger border-0 px-3 py-2 rounded-3 hover-shadow transition">
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

    {{-- CSS Inline Improvement (Optional for the hover effect) --}}
    
@endsection