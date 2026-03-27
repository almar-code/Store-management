@extends('Layouts.master')
@section('link')
<link href="assets/css/order.css" rel="stylesheet">
@endsection

@section('content')
<div class="container my-4 px-2 px-md-2" dir="rtl">
    <div class="container">

        <div class="controls-container">
            <div class="section-ar">
                <div class="section-title" style="margin-bottom: 0px;">
                    <h3 style="font-size: 20px">قائمة <span class="orange-text">الصلاحيات</span></h3>
                </div>
            </div>
            <div class="search-input-container">
                <i class="bi bi-search search-icon"></i>
                   <input type="text" class="search-input" placeholder="Search" id="sectionSearch">
            </div>

            <a href="/permission" class="btn-add-product shadow-sm" style="justify-content: center;" title="تحديث الصفحة">
                <i class="bi bi-arrow-clockwise" ></i>

            </a>
        </div>

        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>متسلسل</th>
                        <th>الاسم</th>
                        <th>الصلاحية</th>
                        <th>النشاط</th>
                        <th>عمليات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($permissions as $permission)
                    <tr class="card-item">
                        <td>{{ $loop->iteration }}</td>
                        <td class="card-title">{{ $permission->user->full_name }}</td>
                        <td class="card-text">{{ $permission->permission->permission_name }}</td>

                        <!-- النشاط -->
                       <td>
    <div style="
    margin-top: 5px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background-color: {{ $permission->is_active ? '#008870' : '#ff0000' }};
        display: inline-block;
    " title="{{ $permission->is_active ? 'المستخدم نشط' : 'المستخدم غير نشط' }}"></div>
</td>


                        <!-- العمليات -->
                          <td>
                                    <div class="dropdown">
                                        <button class="btn text-dark p-0 border-0 shadow-none " type="button"
                                            data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
                                            <i class="bi bi-three-dots-vertical fs-4"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-start shadow border-light">
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center justify-content-between py-2 text-end"
                                                    href="/edit-permission/{{ $permission->user_id }}/{{ $permission->permission_id }}">
                                                    <span class="ms-2">تعديل</span>
                                                    <i class="bi bi-pencil-square text-turquoise"></i>
                                                </a>
                                            </li>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center justify-content-between py-2 text-danger font-weight-bold text-end"
                                                    href="#"
                                                onclick="confirmDelete('/delete-permission/{{ $permission->user_id }}/{{ $permission->permission_id }}','حذف صلاحية','هل أنت متأكد من حذف هذا الصلاحية')">
                                                    
                                                    <span class="ms-2">حذف الصلاحية</span>
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
