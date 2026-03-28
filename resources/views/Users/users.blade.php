@extends('layouts.master')

@section('link')
    <link href="{{ asset('assets/css/order.css') }}?v={{ time() }}" rel="stylesheet">
<link href="{{ asset('assets/css/login.css') }}?v={{ time() }}" rel="stylesheet">
@endsection

@section('content')
<div class="container my-4 px-2 px-md-2" dir="rtl">
    <div class="container">
        <div class="controls-container">
            <div class="section-ar">
                <div class="section-title" style="margin-bottom: 0px;">
                    <h3 style="font-size: 20px">قائمة <span class="orange-text">المستخدمين</span></h3>
                </div>
            </div>

            <div class="search-input-container" id="tour-search-box" data-intro="من هنا يمكنك البحث عن المستخدمين بالاسم بسرعة" data-step="1">
                <i class="bi bi-search search-icon"></i>
                <input type="text" class="search-input" placeholder="Search" id="sectionSearch">
            </div>

            <a href="/addUser" id="tour-add-btn" class="btn-add-product shadow-sm" data-intro="أضف مستخدم جديد للنظام من هذا الزر" data-step="2">
                إضافة مستخدم
                <i class="bi bi-plus-lg ms-2"></i>
            </a>
           <i class="bi bi-question-circle text-turquoise fs-4" 
            onclick="startTour()" 
            style="cursor: pointer; margin-right: 10px;" 
            title="مساعدة - Help">
            </i>
        </div>

        <div class="table-container">
            <table id="tour-table" class="table" data-intro="جدول المستخدمين: الأسماء باللون الأخضر تعني أن المستخدم يمتلك صلاحيات وصول، بينما يمكنك إضافة صلاحيات للبقية عن طريق الضغط على الثلاث نقاط ." data-step="3">
                <thead>
                    <tr>
                        <th>الرقم</th>
                        <th>اسم المستخدم</th>
                        <th>الاسم</th>
                        <th>الايميل</th>
                        <th>العنوان</th>
                        <th data-intro= " عمود العمليات : من هنا يمكنك تعديل المستخدم و اضافه صلاحيه للمستخدم اذا لم يكن لديه صلاحيه  او جذف المستخدم " data-step="4">العمليات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                    @php
                        // نتحقق مرة واحدة هل المستخدم لديه صلاحيات أم لا
                        $hasPermissions = $user->userPermissions->isNotEmpty();
                        // نحدد اسم الكلاس بناءً على الحالة
                        $colorClass = $hasPermissions ? 'text-success fw-bold' : ''; 
                    @endphp
                    <tr class="card-item">
                        
                        <td class="{{$colorClass }}" >{{$loop->iteration}}</td>
                        <td class="card-title {{$colorClass }}" >{{$user->full_name}}</td>
                        <td class="{{$colorClass }}">{{$user->username}}</td>
                        <td class="{{$colorClass }}">{{ str_repeat('.', 5) . substr($user->email, 0, 3) }}</td>
                        <td class="{{$colorClass }}">{{$user->address}}</td>
                        <td>
                            <div class="dropdown">
                                <button class="btn text-dark p-0 border-0 shadow-none" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-three-dots-vertical fs-4"></i>
                                </button>
                                
                                <ul class="dropdown-menu dropdown-menu-end shadow border-light">
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center justify-content-between py-2 text-end" href="/edit-user/{{ $user->user_id }}">
                                            <span class="ms-2">تعديل</span>
                                            <i class="bi bi-pencil-square text-success"></i>
                                        </a>
                                    </li>
                                    @if($user->userPermissions->isEmpty())
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center justify-content-between py-2 text-end" href="/addPermission/{{ $user->user_id }}">
                                            <span class="ms-2">إضافة صلاحية</span>
                                            <i class="bi bi-plus-circle text-primary"></i>
                                        </a>
                                    </li>
                                    @endif
                                    
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center justify-content-between py-2 text-danger font-weight-bold text-end" href="#" onclick="confirmDelete('/delete-user/{{ $user->user_id }}','حذف المستخدم ','هل أنت متأكد؟')">
                                            <span class="ms-2">حذف المستخدم</span>
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
