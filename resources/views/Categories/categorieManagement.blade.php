@extends('layouts.master')
@section('content')

<div class="main-content-wrapper" style="min-height: 100vh; direction: rtl;">
    <div class="container">

        <div class="controls-container">
                <div class="section-ar">

                    <div class="section-title" style="margin-bottom: 0px;">
                        <h3 style="color: white ;font-size: 20px">قائمة <span class="orange-text">المنتجات</span></h3>
                    </div>
                </div>
                <div class="search-input-container">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" class="search-input" placeholder="Search">
                </div>

                <a href="#" class="btn-add-product shadow-sm">
                    إضافة منتج
                    <i class="bi bi-plus-lg ms-2"></i>
                </a>
            </div>

        <div class="row g-3">
            @php
                $categories = [
                    (object)['id'=>101, 'name'=>'إلكترونيات', 'section'=>(object)['name'=>'الأجهزة الرقمية'], 'image'=>'https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=100&q=80'],
                    (object)['id'=>102, 'name'=>'ملابس رجالية', 'section'=>(object)['name'=>'الأزياء'], 'image'=>'https://images.unsplash.com/photo-1523381210434-271e8be1f52b?auto=format&fit=crop&w=100&q=80'],
                    (object)['id'=>103, 'name'=>'أحذية رياضية', 'section'=>(object)['name'=>'الأحذية'], 'image'=>'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=100&q=80']
                ];
            @endphp

            @forelse($categories as $category)
                <div class="col-12">
                    <div class="card bg-white text-dark border-0 shadow-sm hover-card transition">
                        <div class="card-body d-flex align-items-center justify-content-between flex-wrap py-3">
                            
                            <div class="d-flex align-items-center">
                                <div class="image-holder ms-3"> <img src="{{ $category->image }}" class="rounded-circle border" width="60" height="60" alt="category" style="object-fit: cover;">
                                </div>
                                <div class="text-end">
                                    <h5 class="mb-0 fw-bold" style="color: #2d3436;">{{ $category->name }}</h5>
                                    <small class="text-muted fw-medium">{{ $category->section->name ?? '-' }}</small>
                                </div>
                            </div>

                            <div class="d-flex align-items-center">
                                <span class="ms-4 text-secondary d-none d-md-inline" style="font-size: 0.9rem;">ID: #{{ $category->id }}</span>
                                <a href="#" class="btn btn-sm btn-outline-dark ms-2 px-3 border-secondary-subtle">
                                    <i class="bi bi-pencil-square ms-1"></i> تعديل
                                </a>
                                <a href="#" class="btn btn-sm btn-outline-danger px-3">
                                    <i class="bi bi-trash ms-1"></i> حذف
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