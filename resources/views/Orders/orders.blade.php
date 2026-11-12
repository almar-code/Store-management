@extends('Layouts.master')

@section('link')
    <link href="{{ asset('assets/css/orders-premium.css') }}?v={{ time() }}" rel="stylesheet">
    <link href="{{ secure_asset('assets/css/orders-premium.css') }}?v={{ time() }}" rel="stylesheet">
@endsection

@section('content')
<div class="container my-4 px-2 px-md-3 op-page">

    {{-- رأس الصفحة --}}
    <div class="op-head">
        <div>
            <h3 class="op-title">قائمة <span>الطلبات</span></h3>
            <div class="op-sub">تابع الطلبات وحدّث حالتها ليصل الإشعار للعميل</div>
        </div>

        <div class="op-actions">
            <div class="op-search">
                <i class="bi bi-search"></i>
                <input type="text" id="orderSearch" placeholder="ابحث برقم الطلب، الاسم، الجوال أو المدينة">
            </div>
            <a href="/orders" class="op-btn" title="تحديث">
                <i class="bi bi-arrow-clockwise"></i>
            </a>
        </div>
    </div>

    {{-- الإحصائيات --}}
    <div class="op-stats">
        <div class="op-stat">
            <div class="icon"><i class="bi bi-bag-check"></i></div>
            <div>
                <span class="value">{{ $stats['total'] }}</span>
                <span class="label">إجمالي الطلبات</span>
            </div>
        </div>
        <div class="op-stat">
            <div class="icon"><i class="bi bi-hourglass-split"></i></div>
            <div>
                <span class="value">{{ $stats['active'] }}</span>
                <span class="label">طلبات قيد التنفيذ</span>
            </div>
        </div>
        <div class="op-stat">
            <div class="icon"><i class="bi bi-check2-circle"></i></div>
            <div>
                <span class="value">{{ $stats['completed'] }}</span>
                <span class="label">طلبات مكتملة</span>
            </div>
        </div>
        <div class="op-stat">
            <div class="icon"><i class="bi bi-cash-stack"></i></div>
            <div>
                <span class="value">{{ number_format($stats['revenue'], 2) }}</span>
                <span class="label">إيرادات المكتمل (ر.س)</span>
            </div>
        </div>
    </div>

    {{-- تبويبات الحالات --}}
    <div class="op-tabs">
        <button class="op-tab active" data-filter="all">
            الكل <span class="count">{{ $stats['total'] }}</span>
        </button>
        @foreach(\App\Models\Order::STATUSES as $key => $label)
            <button class="op-tab" data-filter="{{ $key }}">
                {{ $label['ar'] }} <span class="count">{{ $statusCounts[$key] ?? 0 }}</span>
            </button>
        @endforeach
    </div>

    {{-- الجدول --}}
    <div class="op-card op-table-card">
        <table class="op-table" id="ordersTable">
            <thead>
                <tr>
                    <th>رقم الطلب</th>
                    <th>العميل</th>
                    <th>المدينة</th>
                    <th>القطع</th>
                    <th>الإجمالي</th>
                    <th>الدفع</th>
                    <th>الحالة</th>
                    <th>التاريخ</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                    @php
                        $name = $order->customer->name ?? 'عميل';
                        $search = mb_strtolower($order->order_id . ' ' . $name . ' ' . ($order->customer->phone ?? '') . ' ' . ($order->address->city ?? ''));
                    @endphp
                    <tr data-status="{{ $order->status }}" data-search="{{ $search }}">
                        <td class="op-td-main">
                            <div class="op-customer">
                                <div class="op-avatar">{{ mb_substr($name, 0, 1) }}</div>
                                <div>
                                    <strong>{{ $name }}</strong>
                                    <small>{{ $order->customer->phone ?? '-' }}</small>
                                </div>
                            </div>
                            <span class="op-id d-md-none">#{{ $order->order_id }}</span>
                        </td>
                        <td data-label="رقم الطلب" class="d-none d-md-table-cell">
                            <span class="op-id">#{{ $order->order_id }}</span>
                        </td>
                        <td data-label="المدينة">{{ $order->address->city ?? '-' }}</td>
                        <td data-label="القطع">{{ (int) $order->items_qty }}</td>
                        <td data-label="الإجمالي"><strong>{{ number_format($order->total_price, 2) }} ر.س</strong></td>
                        <td data-label="الدفع">
                            <span class="pay-{{ $order->payment_status }}">{{ $order->paymentLabel() }}</span>
                        </td>
                        <td data-label="الحالة">
                            <span class="op-status st-{{ $order->status }}">{{ $order->statusLabel() }}</span>
                        </td>
                        <td data-label="التاريخ">{{ $order->created_at->format('Y-m-d') }}</td>
                        <td data-label="التفاصيل">
                            <a href="/orderDetails/{{ $order->order_id }}" class="op-view" title="عرض التفاصيل">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="op-empty" id="ordersEmpty" @if($orders->isNotEmpty()) hidden @endif>
            <i class="bi bi-inbox"></i>
            لا توجد طلبات مطابقة
        </div>
    </div>
</div>
@endsection

@section('jsfile')
<script>
    // البحث والتصفية حسب الحالة
    const rows = document.querySelectorAll('#ordersTable tbody tr');
    const search = document.getElementById('orderSearch');
    const empty = document.getElementById('ordersEmpty');
    let activeFilter = 'all';

    function applyFilter() {
        const q = search.value.trim().toLowerCase();
        let visible = 0;

        rows.forEach(row => {
            const show = (activeFilter === 'all' || row.dataset.status === activeFilter)
                && row.dataset.search.includes(q);
            row.hidden = !show;
            if (show) visible++;
        });

        empty.hidden = visible > 0;
    }

    search.addEventListener('input', applyFilter);

    document.querySelectorAll('.op-tab').forEach(tab => {
        tab.addEventListener('click', () => {
            document.querySelectorAll('.op-tab').forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            activeFilter = tab.dataset.filter;
            applyFilter();
        });
    });
</script>
@endsection