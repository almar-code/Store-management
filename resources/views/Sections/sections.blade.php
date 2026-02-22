@extends('layouts.master')
@section('content')
    <div class="main-content-wrapper" style="min-height: 100vh; direction: rtl;">
        <div class="container">

            <div class="controls-container">
                <div class="section-ar">

                    <div class="section-title" style="margin-bottom: 0px;">
                        <h3 style="font-size: 20px">قائمة <span class="orange-text">الاقسام</span></h3>
                    </div>
                </div>
                <div class="search-input-container">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" class="search-input" placeholder="Search">
                </div>

                <a href="#" class="btn-add-product shadow-sm">
                    إضافة قسم
                    <i class="bi bi-plus-lg ms-2"></i>
                </a>
            </div>

            <div class="row g-3 px-2" dir="rtl">
                @php
                    // تحديث البيانات لتصبح أقسام (Sections) بدلاً من فئات
                    $sections = [
                        (object) [
                            'id' => 1,
                            'name' => 'الرسمية',
                        ],
                        (object) [
                            'id' => 2,
                            'name' => 'تخرج',
                        ],
                        (object) [
                            'id' => 3,
                            'name' => 'الانيقة',
                        ],
                    ];
                @endphp

                @forelse($sections as $section)
                    <div class="col-12" style="border: rgb(210, 210, 210) solid 1px; border-radius: 7px;">
                        <div class="card bg-white text-dark border-0 shadow-sm transition">
                            <div class="card-body d-flex align-items-center justify-content-between py-3">

                                <div class="text-end">
                                    <h6 class="mb-0 fw-bold" style="color: #2d3436;">{{ $section->name }}</h6>
                                </div>

                                <div class="d-flex align-items-center">
                                    <a href="#"
                                        class="btn btn-sm btn-outline-dark px-2 px-md-3 border-secondary-subtle ms-2 shadow-none"
                                        style="font-size: 0.85rem;">
                                        <i class="bi bi-pencil-square ms-1"></i>
                                        تعديل
                                    </a>
                                    <a href="#" class="btn btn-sm btn-outline-danger px-2 px-md-3 shadow-none"
                                        style="font-size: 0.85rem;">
                                        <i class="bi bi-trash ms-1"></i>
                                        حذف
                                    </a>
                                </div>

                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5 bg-white rounded shadow-sm border">
                        <p class="text-muted mb-0">لا توجد أقسام (Sections) حالياً</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
