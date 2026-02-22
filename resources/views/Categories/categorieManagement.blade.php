@extends('layouts.master')
@section('content')
    <div class="main-content-wrapper" style="min-height: 100vh; direction: rtl;">
        <div class="container">

            <div class="controls-container">
                <div class="section-ar">

                    <div class="section-title" style="margin-bottom: 0px;">
                        <h3 style="font-size: 20px">قائمة <span class="orange-text">الفئات</span></h3>
                    </div>
                </div>
                <div class="search-input-container">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" class="search-input" placeholder="Search">
                </div>

                <a href="#" class="btn-add-product shadow-sm">
                    إضافة فئة
                    <i class="bi bi-plus-lg ms-2"></i>
                </a>
            </div>

            <div class="row g-3">
                @php
                    $categories = [
                        (object) [
                            'id' => 101,
                            'name' => 'رسمية',
                            'section' => (object) ['name' => 'عبايات رسمية'],
                            'image' => 'assets/img/photos/a3.jpg',
                        ],
                        (object) [
                            'id' => 102,
                            'name' => 'تخرج',
                            'section' => (object) ['name' => 'عبايات لحفلات تخرج'],
                            'image' => 'assets/img/photos/a3.jpg',
                        ],
                        (object) [
                            'id' => 103,
                            'name' => 'كاجول',
                            'section' => (object) ['name' => 'عبايات  كاجول '],
                            'image' => 'assets/img/photos/a3.jpg',
                        ],
                    ];
                @endphp

                @forelse($categories as $category)
                    <div class="col-12" style="border: rgb(136, 136, 136) solid 1px; border-radius: 7px;">
                        <div class="card bg-white text-dark border-0 shadow-sm hover-card transition">
                            <div class="card-body d-flex align-items-center justify-content-between py-3">

                                <div class="d-flex align-items-center">
                                    <div class="image-holder ms-3">
                                        <img src="{{ $category->image }}" class="rounded-circle border" width="60"
                                            height="60" alt="category" style="object-fit: cover;">
                                    </div>
                                    <div class="text-end">
                                        <h5 class="mb-0 fw-bold" style="color: #2d3436;">{{ $category->name }}</h5>
                                        <small class="card-text mb-2 small text-muted text-truncate">
                                            {{ Str::words($category->section->name ?? '-', 2, '...') }}
                                        </small>
                                    </div>
                                </div>

                                <div class="d-flex flex-column flex-md-row align-items-end align-items-md-center">
                                    <a href="#"
                                        class="btn btn-sm btn-outline-dark px-3 border-secondary-subtle mb-2 mb-md-0 ms-md-2">
                                        <i class="bi bi-pencil-square ms-1"></i> تعديل
                                    </a>
                                    <a href="#" class="btn btn-sm btn-outline-danger px-3 ">
                                        <i class="bi bi-trash ms-1 "> </i> حذف
                                    </a>
                                </div>

                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5 bg-white rounded shadow-sm">
                        <p class="text-muted">لا توجد فئات حالياً</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
