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
                        قائمة <span class="orange-text">الصلاحيات</span>
                        <span class="at-count">{{ $permissions->count() }}</span>
                    </h3>
                </div>
            </div>

            <div class="search-input-container">
                <i class="bi bi-search search-icon"></i>
                <input type="text" class="search-input" placeholder="ابحث عن مستخدم أو صلاحية..." id="sectionSearch">
            </div>

            <a href="/permission" class="at-btn" title="تحديث الصفحة">
                <i class="bi bi-arrow-clockwise"></i>
            </a>

            <i class="bi bi-question-circle text-turquoise fs-4"
               onclick="startTour()"
               style="cursor: pointer; margin-right: 10px;"
               title="مساعدة - Help"></i>
        </div>

        <div class="table-container table-responsive">
            <table class="table align-middle at-table" data-intro="جدول صلاحيات المستخدمين : هنا يمكنك مراجعة وتدقيق الصلاحيات الممنوحة لكل موظف في النظام ." data-step="1">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الاسم</th>
                        <th data-intro="هذا العمود يوضح المسمى الوظيفي أو الصلاحيه التي يمتلكه المستخدم." data-step="2">الصلاحية</th>
                        <th data-intro="حالة الصلاحية: (نشط) يمتلك صلاحيه دخول، (غير نشط) الصلاحية مجمدة مؤقتاً." data-step="3">الحالة</th>
                        <th class="text-center" data-intro="العمليات : عند الضغط على الثلاث نقاط يمكنك تعديل صلاحيه المستخدم او يمكنك حذف الصلاحيه وابقاء المستخدم بدون صلاحيه ." data-step="4">العمليات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($permissions as $permission)
                    <tr class="card-item">
                        <td><span class="at-num">{{ $loop->iteration }}</span></td>
                        <td class="card-title fw-bold">{{ $permission->user->full_name }}</td>
                        <td>
                            <span class="at-pill navy card-text">{{ $permission->permission->permission_name }}</span>
                        </td>
                        <td>
                            <span class="at-pill {{ $permission->is_active ? 'green' : 'red' }}">
                                <span class="at-dot"></span>{{ $permission->is_active ? 'نشط' : 'غير نشط' }}
                            </span>
                        </td>
                        <td class="text-center">
                            @if($permission->user->role !== 'admin')
                                <div class="dropdown">
                                    <button class="at-actions-btn" type="button" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false" title="العمليات">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-start shadow border-light at-menu">
                                        <li>
                                            <a class="dropdown-item text-end" href="/edit-permission/{{ $permission->user_id }}/{{ $permission->permission_id }}">
                                                <i class="bi bi-pencil-square text-turquoise"></i>
                                                <span>تعديل</span>
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item text-danger text-end" href="#"
                                               onclick="confirmDelete('/delete-permission/{{ $permission->user_id }}/{{ $permission->permission_id }}','حذف صلاحية','هل أنت متأكد من حذف هذه الصلاحية؟')">
                                                <i class="bi bi-trash3"></i>
                                                <span>حذف الصلاحية</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            @else
                                <span class="at-pill gray" title="صلاحية المدير لا يمكن تعديلها">
                                    <i class="bi bi-lock"></i> غير قابل للتعديل
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="at-empty">
                            <i class="bi bi-shield-lock"></i>
                            لا توجد صلاحيات حالياً
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
