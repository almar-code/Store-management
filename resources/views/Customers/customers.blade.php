@extends('Layouts.master')
@section('content')
    <div class="main-content-wrapper" style="min-height: 100vh; direction: rtl;">
        <div class="container">

            <div class="controls-container">
                <div class="section-ar">
                    <div class="section-title" style="margin-bottom: 0px;">
                        <h3 style="font-size: 20px">قائمة <span class="orange-text">العملاء</span></h3>
                    </div>
                </div>
                <div class="search-input-container">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" class="search-input" placeholder="بحث..." id="sectionSearch">
                </div>

                <a href="/customers" class="btn-add-product shadow-sm" style="justify-content: center ;" title="تحديث الصفحة">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            </div>

            <div class="row g-4 px-2">
                @forelse($customers as $customer)
                    <div class="card card-item border-0 shadow-sm rounded-4 overflow-hidden position-relative bg-white" title="رقم العميل: {{ $customer->phone ?? '-' }} ">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center">

                                {{-- قسم الصورة مع التحقق --}}
                                <div class="image-holder ms-3">
                                    <div class="p-1 bg-white rounded-circle border shadow-sm">
                                        @if($customer->profile_image && file_exists(public_path('storage/uploads/subcategory/'.$customer->profile_image)))
                                            <img src="{{ asset('storage/uploads/subcategory/'.$customer->profile_image) }}" class="rounded-circle" width="55" height="55" style="object-fit: cover;">
                                        @else
                                            {{-- أيقونة المستخدم الافتراضية --}}
                                            <div class="rounded-circle d-flex align-items-center justify-content-center bg-light" style="width: 55px; height: 55px;">
                                                <i class="bi bi-person-fill text-secondary" style="font-size: 25px;"></i>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- بيانات العميل --}}
                                <div class="text-end flex-grow-1 overflow-hidden">
                                    <h6 class="card-title mb-1 fw-bold text-truncate" style="font-size:14px; color: #000;">
                                        {{ $customer->name ?? 'عميل غير معروف' }}
                                    </h6>
                                    <div class="d-flex flex-column gap-1">
                                        <span class="card-text text-secondary" style="font-size:11px;">
                                            <i class="bi bi-envelope-at me-1"></i> {{ $customer->email ?? '-' }}
                                        </span>
                                        {{-- تنسيق التاريخ --}}
                                        <span class="text-muted" style="font-size: 10px;">
                                            <i class="bi bi-calendar3 me-1"></i>
                                            {{ $customer->created_at->format('Y-m-d') }}
                                        </span>
                                    </div>
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
                            <p class="text-muted fw-bold">لا يوجد عملاء حالياً</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection