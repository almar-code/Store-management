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
                        قائمة <span class="orange-text">حسابات الدفع</span>
                        <span class="at-count">{{ $accounts->count() }}</span>
                    </h3>
                </div>
            </div>

            <div class="search-input-container" id="tour-search-box"
                 data-intro="من هنا يمكنك البحث عن حسابات الدفع بسرعة" data-step="1">
                <i class="bi bi-search search-icon"></i>
                <input type="text" class="search-input" placeholder="ابحث عن حساب..." id="sectionSearch">
            </div>

            <a href="{{ url('store_payment_accounts') }}" class="at-btn" title="تحديث الصفحة">
                <i class="bi bi-arrow-clockwise"></i>
            </a>

            <a href="{{ url('addStorePaymentAccount') }}" class="at-btn-solid" title="إضافة حساب جديد">
                <i class="bi bi-plus-lg"></i>
                <span class="at-btn-text">إضافة حساب</span>
            </a>
        </div>

        <div class="table-container table-responsive">
            <table class="table align-middle at-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>نوع الحساب</th>
                        <th>اسم المزود</th>
                        <th>صاحب الحساب</th>
                        <th>رقم الحساب</th>
                        <th>الحالة</th>
                        <th class="text-center">العمليات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($accounts as $account)
                    <tr class="card-item">
                        <td><span class="at-num">{{ $loop->iteration }}</span></td>
                        <td>
                            @if($account->account_type == 'bank')
                                <span class="at-pill blue"><i class="bi bi-bank"></i> بنكي</span>
                            @elseif($account->account_type == 'wallet')
                                <span class="at-pill green"><i class="bi bi-wallet2"></i> محفظة</span>
                            @else
                                <span class="at-pill gray">{{ $account->account_type ?? '-' }}</span>
                            @endif
                        </td>
                        <td class="card-title fw-bold">{{ $account->provider_name ?? '-' }}</td>
                        <td class="card-text">{{ $account->account_holder_name ?? '-' }}</td>
                        <td class="at-muted at-nowrap" dir="ltr">{{ $account->account_number ?? $account->iban ?? '-' }}</td>
                        <td>
                            {{-- الضغط يبدل حالة الحساب --}}
                            <a href="/update-account/{{ $account->store_payment_account_id }}"
                               class="at-pill {{ $account->is_active ? 'green' : 'red' }}"
                               title="{{ $account->is_active ? 'اضغط لإيقاف الحساب' : 'اضغط لتفعيل الحساب' }}">
                                <span class="at-dot"></span>{{ $account->is_active ? 'نشط' : 'غير نشط' }}
                            </a>
                        </td>
                        <td class="text-center">
                            <div class="dropdown">
                                <button class="at-actions-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="العمليات">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>

                                <ul class="dropdown-menu dropdown-menu-end shadow border-light at-menu">
                                    <li>
                                        <a class="dropdown-item text-end" href="{{ url('/edit-store-payment-account/' . $account->store_payment_account_id) }}">
                                            <i class="bi bi-pencil-square text-success"></i>
                                            <span>تعديل</span>
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item text-danger text-end" href="#"
                                           onclick="confirmDelete('{{ url('/delete-store-payment-account/' . $account->store_payment_account_id) }}','حذف حساب الدفع','هل أنت متأكد من حذف هذا الحساب؟')">
                                            <i class="bi bi-trash3"></i>
                                            <span>حذف الحساب</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="at-empty">
                            <i class="bi bi-credit-card"></i>
                            لا توجد حسابات دفع حالياً
                            <br>
                            <a href="{{ url('addStorePaymentAccount') }}" class="at-btn-solid">
                                <i class="bi bi-plus-lg"></i> إضافة حساب
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
