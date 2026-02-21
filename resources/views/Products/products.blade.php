@extends('layouts.master')
@section('content')

<div class="container my-4  px-2 px-md-2" dir="rtl">
<div class="search-wrapper">
    <div class="search-input-container">
        <i class="bi bi-search search-icon"></i>
        <input type="text" class="search-input" placeholder="Search">
    </div>
</div>
    <div class="row g-0"> 
        @for ($i = 0; $i < 10; $i++)
        <div class="col-12 col-lg-4 mt-3" >
            <div class="card product-card text-white border-secondary h-100 shadow-sm mx-2 ">
                <div class="row g-0 align-items-center px-3 py-2">
                    
                    <div class="col-4 col-md-3">
                        <img src="assets/img/photos/a3.jpg" 
                             class="img-fluid rounded shadow-sm" 
                             alt="Product Image" 
                             style="aspect-ratio: 1/1; object-fit: cover; border-radius: 12px !important;">
                    </div>

                    <div class="col-7 col-md-8 px-3 text-end">
                        <div class="d-flex flex-column">
                            <h5 class="card-title mb-1 fw-bold text-truncate">عباية رسمية</h5>
                            <p class="card-text mb-2 small text-truncate">عباية رسمية للمقابلات والحفلات الرسمية</p>
                            <div class="fw-bold" style="font-size: 1rem;">
                                66.00 <span class="small text-turquoise" style="font-size: 0.8rem;">ر.س</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-1 text-start align-self-start">
                        <div class="dropdown">
                           <button class="btn text-white p-0 border-0 shadow-none" type="button" data-bs-toggle="dropdown" data-bs-boundary="viewport" aria-expanded="false">
                                <i class="bi bi-three-dots-vertical fs-4"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-start shadow border-secondary">
                                <li>
                                    <a class="dropdown-item d-flex align-items-center justify-content-between py-2" href="#">
                                        <span class="ms-2">تعديل</span>
                                        <i class="bi bi-pencil-square text-turquoise"></i>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center justify-content-between py-2" href="#">
                                        <span class="ms-2">اضافة مقاس</span>
                                        <i class="bi bi-plus-circle text-turquoise"></i> 
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider bg-secondary"></li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center justify-content-between py-2 text-danger font-weight-bold" href="#">
                                        <span class="ms-2">حذف المنتج</span>
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        @endfor
    </div>
</div>

@endsection