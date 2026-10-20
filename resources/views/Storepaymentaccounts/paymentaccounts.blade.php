@extends('Layouts.master')

@section('link')
    <link href="{{ secure_asset('assets/css/order.css') }}?v={{ time() }}" rel="stylesheet">
    <link href="{{ asset('assets/css/order.css') }}?v={{ time() }}" rel="stylesheet">
    <link href="{{ secure_asset('assets/css/login.css') }}?v={{ time() }}" rel="stylesheet">
    <link href="{{ asset('assets/css/login.css') }}?v={{ time() }}" rel="stylesheet">
@endsection


@section('content')
<div class="container my-4 px-2 px-md-2" dir="rtl">
    <div class="container">
        <div class="controls-container">
            <div class="section-ar">
                <div class="section-title" style="margin-bottom: 0px;">
                    <h3 style="font-size: 20px">قائمة <span class="orange-text">حسابات الدفع</span></h3>
                </div>
            </div>
            <div class="search-input-container" id="tour-search-box"
                    data-intro="من هنا يمكنك البحث عن المستخدمين بالاسم بسرعة" data-step="1">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" class="search-input" placeholder="Search" id="sectionSearch">
                </div>

            <a href="{{ url('store_payment_accounts') }}" class="btn-add-product shadow-sm" style="justify-content: center;" title="تحديث الصفحة">
                <i class="bi bi-arrow-clockwise"></i>
            </a>

            <a href="{{ url('addStorePaymentAccount') }}" class="btn-add-product shadow-sm ms-2" style="justify-content: center; background-color: #008870; color: #fff;" title="إضافة حساب جديد">
                <i class="bi bi-plus fs-5"></i>
            </a>
        </div>

        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>متسلسل</th>
                        <th>نوع الحساب</th>
                        <th>اسم المزود</th>
                        <th>صاحب الحساب</th>
                        <th>رقم الحساب </th>
                        <th>النشاط</th>
                        <th>العمليات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($accounts as $account)
                    <tr class="card-item">
                        <td class="fs-6 fs-md-5">{{ $loop->iteration }}</td>
                        <td>
                            @if($account->account_type == 'bank') <span class="badge bg-primary">بنكي (Bank)</span>
                            @elseif($account->account_type == 'wallet') <span class="badge bg-success">محفظة (Wallet)</span>
                            @else <span class="badge bg-secondary">{{ $account->account_type ?? '-' }}</span>
                            @endif
                        </td>
                        <td class="card-title fw-bold lh-1 fs-md-5">{{ $account->provider_name ?? '-' }}</td>
                        <td class="lh-1 fs-md-5">{{ $account->account_holder_name ?? '-' }}</td>
                        <td class="text-muted lh-1 fs-md-5">
                            {{ $account->account_number ?? $account->iban ?? '-' }}
                        </td>
                        <td>
                            <a href="/update-account/{{ $account->store_payment_account_id }}">
                                <div style="
                                    margin-top: 5px;
                                    width: 16px;
                                    height: 16px;
                                    border-radius: 50%;
                                    background-color: {{ $account->is_active ? '#008870' : '#ff0000' }};
                                    display: inline-block;
                                " title="{{ $account->is_active ? 'الحساب نشط' : 'الحساب غير نشط' }}"></div>
                            </a>
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn text-dark p-0 border-0 shadow-none" type="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-three-dots-vertical fs-4"></i>
                                </button>

                                <ul class="dropdown-menu dropdown-menu-end shadow border-light">
                                    {{-- زر التعديل --}}
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center justify-content-between py-2 text-end"
                                            href="{{ url('/edit-store-payment-account/' . $account->store_payment_account_id) }}">
                                            <span class="ms-2">تعديل</span>
                                            <i class="bi bi-pencil-square text-success"></i>
                                        </a>
                                    </li>

                                    {{-- زر الحذف --}}
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center justify-content-between py-2 text-danger font-weight-bold text-end"
                                            href="#"
                                            onclick="confirmDelete('{{ url('/delete-store-payment-account/' . $account->store_payment_account_id) }}','حذف حساب الدفع','هل أنت متأكد من حذف هذا الحساب؟')">
                                            <span class="ms-2">حذف الحساب</span>
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