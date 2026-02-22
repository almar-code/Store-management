@extends('layouts.master')
@section('categorieManagement')

<div class="main-content-wrapper py-5" style="background-color: #f8f9fa; min-height: 100vh; direction: rtl;">
    <div class="container">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold text-dark mb-0">إدارة الفئات</h3>
            <a href="/addcategorie" class="btn btn-custom-green d-flex align-items-center text-white px-4 py-2 shadow-sm">
                <i class="bi bi-plus-lg ms-2"></i> إضافة فئة جديدة
            </a>
        </div>

        <div class="row mb-4 g-2">
            <div class="col-md-6">
                <div class="input-group shadow-sm">
                    <span class="input-group-text bg-white border-start-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control border-end-0 py-2 text-end" placeholder="ابحث عن فئة...">
                </div>
            </div>
            <div class="col-md-3">
                <select class="form-select shadow-sm py-2 text-end">
                    <option>ترتيب حسب: الاسم (أ-ي)</option>
                    <option>ترتيب حسب: الاسم (ي-أ)</option>
                </select>
            </div>
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