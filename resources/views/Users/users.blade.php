@extends('Layouts.master')

@section('link')
    <link href="{{ secure_asset('assets/css/order.css') }}?v={{ time() }}" rel="stylesheet">
    <link href="{{ asset('assets/css/order.css') }}?v={{ time() }}" rel="stylesheet">
    <link href="{{ secure_asset('assets/css/login.css') }}?v={{ time() }}" rel="stylesheet">
    <link href="{{ asset('assets/css/login.css') }}?v={{ time() }}" rel="stylesheet">
@endsection

@section('content')
    <div class="container my-4 px-2 px-md-2 at-page" dir="rtl">
        <div class="container">
            <div class="controls-container">
                <div class="section-ar">
                    <div class="section-title" style="margin-bottom: 0px;">
                        <h3 style="font-size: 20px">
                            قائمة <span class="orange-text">المستخدمين</span>
                            <span class="at-count">{{ $users->count() }}</span>
                        </h3>
                    </div>
                </div>

                <div class="search-input-container" id="tour-search-box"
                    data-intro="من هنا يمكنك البحث عن المستخدمين بالاسم بسرعة" data-step="1">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" class="search-input" placeholder="ابحث عن مستخدم..." id="sectionSearch">
                </div>

                <a href="/addUser" id="tour-add-btn" class="at-btn-solid"
                    data-intro="أضف مستخدم جديد للنظام من هذا الزر" data-step="2" title="إضافة مستخدم">
                    <i class="bi bi-plus-lg"></i>
                    <span class="at-btn-text">إضافة مستخدم</span>
                </a>

                <i class="bi bi-question-circle text-turquoise fs-4" onclick="startTour()"
                    style="cursor: pointer; margin-right: 10px;" title="مساعدة - Help"></i>
            </div>

            <div class="table-container table-responsive">
                <table id="tour-table" class="table align-middle at-table"
                    data-intro="جدول المستخدمين: الأسماء باللون الأخضر تعني أن المستخدم يمتلك صلاحيات وصول، بينما يمكنك إضافة صلاحيات للبقية عن طريق الضغط على الثلاث نقاط ."
                    data-step="3">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الاسم</th>
                            <th>اسم المستخدم</th>
                            <th>الايميل</th>
                            <th>رقم الهاتف</th>
                            <th>العنوان</th>
                            <th class="text-center"
                                data-intro=" عمود العمليات : من هنا يمكنك تعديل المستخدم و اضافه صلاحيه للمستخدم اذا لم يكن لديه صلاحيه  او جذف المستخدم "
                                data-step="4">العمليات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            @php
                                // المستخدم الذي لديه صلاحيات يظهر اسمه بالأخضر
                                $hasPermissions = $user->userPermissions->isNotEmpty();
                            @endphp
                            <tr class="card-item">
                                <td><span class="at-num">{{ $loop->iteration }}</span></td>
                                <td class="card-title fw-bold {{ $hasPermissions ? 'at-accent' : '' }}">{{ $user->full_name }}</td>
                                <td class="{{ $hasPermissions ? 'at-accent' : 'at-muted' }}">{{ $user->username }}</td>
                                <td class="at-muted">
                                    <span title="{{ $user->email }}" dir="ltr">{{ \Illuminate\Support\Str::limit($user->email, 5, '...') }}</span>
                                </td>
                                <td class="at-muted at-nowrap" dir="ltr">{{ $user->phone ?: '-' }}</td>
                                <td class="at-muted">{{ $user->address ?: '-' }}</td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button class="at-actions-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="العمليات">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>

                                        <ul class="dropdown-menu dropdown-menu-end shadow border-light at-menu">
                                            <li>
                                                <a class="dropdown-item text-end" href="/edit-user/{{ $user->user_id }}">
                                                    <i class="bi bi-pencil-square text-success"></i>
                                                    <span>تعديل</span>
                                                </a>
                                            </li>

                                            @if ($user->role !== 'admin')
                                                <li>
                                                    <a class="dropdown-item text-end" href="/addPermission/{{ $user->user_id }}">
                                                        <i class="bi bi-plus-circle text-primary"></i>
                                                        <span>إضافة صلاحية</span>
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <a class="dropdown-item text-danger text-end" href="#"
                                                        onclick="confirmDelete('/delete-user/{{ $user->user_id }}','حذف المستخدم ','هل أنت متأكد؟')">
                                                        <i class="bi bi-trash3"></i>
                                                        <span>حذف المستخدم</span>
                                                    </a>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="at-empty">
                                    <i class="bi bi-people"></i>
                                    لا يوجد مستخدمون حالياً
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
