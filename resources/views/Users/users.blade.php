@extends('layouts.master')

@section('link')
    <link href="assets/css/order.css" rel="stylesheet">
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
            style="cursor: pointer; margin-right: 15px;" 
            title="مساعدة - Help">
            </i>
        </div>

        <div class="table-container">
            <table id="tour-table" class="table" data-intro="هذا الجدول يعرض كافة بيانات المستخدمين المسجلين" data-step="3">
                <thead>
                    <tr>
                        <th>الرقم</th>
                        <th>اسم المستخدم</th>
                        <th>الاسم</th>
                        <th>الايميل</th>
                        <th>العنوان</th>
                        <th>العمليات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                    <tr class="card-item">
                        <td>{{$loop->iteration}}</td>
                        <td class="card-title">{{$user->full_name}}</td>
                        <td>{{$user->username}}</td>
                        <td>{{ str_repeat('.', 5) . substr($user->email, 0, 3) }}</td>
                        <td>{{$user->address}}</td>
                        <td data-intro= " من هنا يمكنك تعديل المستخدم و اضافه صلاحيه  او جذف المستخدم " data-step="4">
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
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center justify-content-between py-2 text-end" href="/addPermission/{{ $user->user_id }}">
                                            <span class="ms-2">إضافة صلاحية</span>
                                            <i class="bi bi-plus-circle text-primary"></i>
                                        </a>
                                    </li>
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
<script>
    function startTour() {
        introJs().setOptions({
            nextLabel: 'التالي',
            prevLabel: 'السابق',
            doneLabel: 'تم',
            showProgress: true,
            showBullets: true,
            overlayOpacity: 0.5,
            disableInteraction: false // السماح للمستخدم بالضغط على النقاط أثناء الشرح
        }).start();
    }
</script>

@endsection
