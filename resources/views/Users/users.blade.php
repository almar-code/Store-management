@extends('layouts.master')
@section('link')
    <link href="assets/css/order.css" rel="stylesheet">
@endsection
@section('content')
    <div class="container my-4  px-2 px-md-2" dir="rtl">
        <div class="container">

            <div class="controls-container">

                <div class="section-ar">

                    <div class="section-title" style="margin-bottom: 0px;">
                        <h3 style="font-size: 20px">قائمة <span class="orange-text">المستخدمين</span></h3>
                    </div>
                </div>
                <div class="search-input-container">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" class="search-input" placeholder="Search">
                </div>

                <a href="#" class="btn-add-product shadow-sm">
                    إضافة مستخدم
                    <i class="bi bi-plus-lg ms-2"></i>
                </a>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>اسم المستخدم</th>
                            <th>رقم الهاتف</th>
                            <th>العنوان</th>
                            <th>عمليات</th>
                        
                        </tr>
                    </thead>
                    <tbody>
                         <tr>
                            <td>1</td>
                            <td>سارة محمد</td>
                            <td>779271679</td>
                            <td>الرياض</td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn text-dark p-0 border-0 shadow-none " type="button"
                                        data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
                                        <i class="bi bi-three-dots-vertical fs-4"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-start shadow border-light">
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center justify-content-between py-2 text-end"
                                                href="#">
                                                <span class="ms-2">تعديل</span>
                                                <i class="bi bi-pencil-square text-turquoise"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center justify-content-between py-2 text-end"
                                                href="/addPermission">
                                                <span class="ms-2">اضافة صلاحية</span>
                                                <i class="bi bi-plus-circle text-turquoise"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center justify-content-between py-2 text-danger font-weight-bold text-end"
                                                href="#">
                                                <span class="ms-2">حذف المنتج</span>
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
