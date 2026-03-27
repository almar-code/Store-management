@extends('Layouts.master')
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
                    <input type="text" class="search-input" placeholder="Search" id="sectionSearch">

                </div>

                <a href="/addsection" class="btn-add-product shadow-sm">
                    إضافة قسم
                    <i class="bi bi-plus-lg ms-2"></i>
                </a>
            </div>
            <div class="row g-3 px-2">

                @forelse($sections as $section)
                    <div class="card-item col-md-4 col-lg-12">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden position-relative bg-white">
                            <div class="card-body p-2">

                                <div class="d-flex align-items-center justify-content-between">

                                    <div class=" text-end flex-grow-1 overflow-hidden">

                                        <h6 class="card-title mb-1 fw-bold text-truncate "
                                            style="font-size:13px; white-space:nowrap; color: #000;">
                                            {{ $section->cat_name ?? '' }}
                                        </h6>


                                    </div>
                                    <div class="d-flex align-items-center gap-2 ms-lg-3">

                                        <a href="/edit-section/{{ $section->cat_id }}"
                                            class="btn btn-sm btn-lg-lg btn-light border-0 rounded-3 px-2 px-lg-3 py-1 py-lg-2"
                                            style="color:#008870; font-size:13px;">
                                            <i class="bi bi-pencil-square"></i>
                                            تعديل
                                        </a>

                                        <a href="#"
                                            onclick="confirmDelete('/delete-section/{{ $section->cat_id }}','حذف قسم؟','هل أنت متأكد من حذف هذا القسم؟')"
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
                    </div>

                @empty
                    <div class="col-12">
                        <div class="text-center py-5 bg-white rounded-4 shadow-sm border border-dashed">
                            <i class="bi bi-inbox text-muted display-4 mb-3"></i>
                            <p class="text-muted fw-bold">لا توجد اقسام حالياً</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    
@endsection
