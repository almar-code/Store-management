@extends('Layouts.master')

@section('link')
    <link href="{{ asset('assets/css/orders-premium.css') }}?v={{ time() }}" rel="stylesheet">
    <link href="{{ secure_asset('assets/css/orders-premium.css') }}?v={{ time() }}" rel="stylesheet">
@endsection

@section('content')
@php
    $steps = ['pending', 'processing', 'ready', 'shipped', 'completed'];
    $stepIcons = ['bi-receipt', 'bi-gear', 'bi-box-seam', 'bi-truck', 'bi-check2'];
    $currentStep = array_search($order->status, $steps);
    $isStopped = in_array($order->status, ['cancelled', 'refunded']);
    $subtotal = $order->orderItems->sum(fn ($i) => $i->price * $i->quantity);
    $phone = $order->customer->phone ?? null;
@endphp

<div class="container my-4 px-2 px-md-3 op-page">

    {{-- رأس الصفحة --}}
    <div class="op-head">
        <div class="d-flex align-items-center gap-3">
            <a href="/orders" class="op-back" title="رجوع"><i class="bi bi-arrow-right"></i></a>
            <div>
                <h3 class="op-title">تفاصيل <span>الطلب #{{ $order->order_id }}</span></h3>
                <div class="op-sub">{{ $order->created_at->format('Y-m-d • h:i A') }}</div>
            </div>
        </div>

        <div class="op-actions">
            <span id="orderStatus" class="op-status st-{{ $order->status }}">{{ $order->statusLabel() }}</span>

            {{-- تغيير الحالة --}}
            <div class="op-status-select" id="statusSelect">
                <button type="button" class="op-btn primary" id="statusToggle">
                    <i class="bi bi-arrow-repeat"></i> تغيير الحالة
                </button>
                <div class="op-status-menu">
                    @foreach(\App\Models\Order::STATUSES as $key => $label)
                        <button type="button" data-status="{{ $key }}" data-label="{{ $label['ar'] }}"
                                class="{{ $order->status === $key ? 'active' : '' }}">
                            <span class="op-dot dot-{{ $key }}"></span>{{ $label['ar'] }}
                        </button>
                    @endforeach
                </div>
            </div>

            <a href="/api/orders/{{ $order->order_id }}/invoice" target="_blank" class="op-btn" title="الفاتورة">
                <i class="bi bi-printer"></i>
            </a>
        </div>
    </div>

    {{-- مراحل الطلب --}}
    <div class="op-card mb-3">
        <div class="op-card-body">
            <div class="op-steps" id="statusSteps" @if($isStopped) hidden @endif>
                @foreach($steps as $i => $step)
                    <div class="op-step {{ $currentStep !== false && $i < $currentStep ? 'done' : '' }} {{ $currentStep === $i ? 'current' : '' }}">
                        <div class="dot"><i class="bi {{ $stepIcons[$i] }}"></i></div>
                        {{ \App\Models\Order::STATUSES[$step]['ar'] }}
                    </div>
                @endforeach
            </div>

            <div class="op-stopped" id="statusStopped" @if(!$isStopped) hidden @endif>
                <i class="bi bi-x-octagon"></i>
                <span>هذا الطلب <span id="stoppedLabel">{{ $order->statusLabel() }}</span> ولن يكمل مراحل التوصيل</span>
            </div>
        </div>
    </div>

    <div class="op-layout">

        {{-- عناصر الطلب --}}
        <div class="op-card">
            <div class="op-card-head">
                <span><i class="bi bi-bag"></i>عناصر الطلب</span>
                <span class="op-sub m-0">{{ $order->orderItems->sum('quantity') }} قطعة</span>
            </div>
            <div class="op-card-body">
                @foreach($order->orderItems as $item)
                    <div class="op-item">
                        <img src="{{ asset('storage/uploads/products/' . ($item->product->p_image ?? '')) }}"
                             alt="" onerror="this.style.visibility='hidden'">

                        <div class="op-item-info">
                            <div class="op-item-name">{{ $item->product->p_name ?? 'منتج محذوف' }}</div>
                            <div class="op-chips">
                                @if($item->color)
                                    <span class="op-chip">
                                        <span class="swatch" style="background: {{ $item->color->color_code }}"></span>
                                        {{ $item->color->color_name }}
                                    </span>
                                @endif
                                @if($item->size)
                                    <span class="op-chip"><i class="bi bi-rulers"></i> {{ $item->size->size_name }}</span>
                                @endif
                                <span class="op-chip">الكمية: {{ $item->quantity }}</span>
                            </div>
                        </div>

                        <div class="op-item-price">
                            <strong>{{ number_format($item->price * $item->quantity, 2) }} ر.س</strong>
                            <small>{{ $item->quantity }} × {{ number_format($item->price, 2) }}</small>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- الشريط الجانبي --}}
        <div>
            {{-- العميل --}}
            <div class="op-card">
                <div class="op-card-head"><span><i class="bi bi-person"></i>العميل</span></div>
                <div class="op-card-body">
                    <div class="op-customer mb-2">
                        <div class="op-avatar">{{ mb_substr($order->customer->name ?? 'ع', 0, 1) }}</div>
                        <div>
                            <strong>{{ $order->customer->name ?? '-' }}</strong>
                            <small>{{ $order->customer->email ?? '-' }}</small>
                        </div>
                    </div>
                    <div class="op-info">
                        <span>الجوال</span>
                        <span>
                            @if($phone)
                                <a class="op-wa" href="https://wa.me/{{ preg_replace('/\D/', '', $phone) }}" target="_blank">
                                    <i class="bi bi-whatsapp"></i> {{ $phone }}
                                </a>
                            @else - @endif
                        </span>
                    </div>
                </div>
            </div>

            {{-- العنوان --}}
            <div class="op-card">
                <div class="op-card-head"><span><i class="bi bi-geo-alt"></i>عنوان التوصيل</span></div>
                <div class="op-card-body">
                    @if($order->address)
                        <div class="op-info"><span>العنوان</span><span>{{ $order->address->title ?? '-' }}</span></div>
                        <div class="op-info"><span>الدولة</span><span>{{ $order->address->country ?? '-' }}</span></div>
                        <div class="op-info"><span>المدينة</span><span>{{ $order->address->city ?? '-' }}</span></div>
                        <div class="op-info"><span>الشارع</span><span>{{ $order->address->street ?? '-' }}</span></div>
                        <div class="op-info"><span>المبنى</span><span>{{ $order->address->building ?? '-' }}</span></div>
                        <div class="op-info"><span>الرمز البريدي</span><span>{{ $order->address->postal_code ?? '-' }}</span></div>
                    @else
                        <div class="op-sub">لا يوجد عنوان لهذا الطلب</div>
                    @endif
                </div>
            </div>

            {{-- الدفع والملخص --}}
            <div class="op-card">
                <div class="op-card-head"><span><i class="bi bi-credit-card"></i>الدفع والملخص</span></div>
                <div class="op-card-body">
                    <div class="op-info"><span>وسيلة الدفع</span><span>{{ $order->payment->method->method_name ?? 'غير محدد' }}</span></div>
                    <div class="op-info">
                        <span>حالة الدفع</span>
                        <span class="pay-{{ $order->payment_status }}">{{ $order->paymentLabel() }}</span>
                    </div>
                    @if($order->payment?->transaction_id)
                        <div class="op-info"><span>رقم العملية</span><span>{{ $order->payment->transaction_id }}</span></div>
                    @endif
                    <div class="op-info"><span>مجموع المنتجات</span><span>{{ number_format($subtotal, 2) }} ر.س</span></div>
                    <div class="op-info op-total"><span>الإجمالي</span><span>{{ number_format($order->total_price, 2) }} ر.س</span></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="op-toast" id="opToast"></div>
@endsection

@section('jsfile')
<script>
    const STEPS = @json($steps);
    const select = document.getElementById('statusSelect');
    const toggle = document.getElementById('statusToggle');

    // فتح واغلاق القائمة
    toggle.addEventListener('click', e => {
        e.stopPropagation();
        select.classList.toggle('open');
    });
    document.addEventListener('click', () => select.classList.remove('open'));

    // رسالة سريعة
    function showToast(text, isError = false) {
        const toast = document.getElementById('opToast');
        toast.textContent = text;
        toast.classList.toggle('error', isError);
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 2500);
    }

    // تحديث الواجهة بعد تغيير الحالة
    function renderStatus(status, label) {
        const badge = document.getElementById('orderStatus');
        badge.className = 'op-status st-' + status;
        badge.textContent = label;

        select.querySelectorAll('[data-status]').forEach(b =>
            b.classList.toggle('active', b.dataset.status === status));

        const stopped = ['cancelled', 'refunded'].includes(status);
        document.getElementById('statusSteps').hidden = stopped;
        document.getElementById('statusStopped').hidden = !stopped;
        document.getElementById('stoppedLabel').textContent = label;

        const current = STEPS.indexOf(status);
        document.querySelectorAll('.op-step').forEach((step, i) => {
            step.classList.toggle('done', i < current);
            step.classList.toggle('current', i === current);
        });
    }

    // ارسال الحالة الجديدة
    select.querySelectorAll('[data-status]').forEach(btn => {
        btn.addEventListener('click', () => {
            const { status, label } = btn.dataset;
            select.classList.remove('open');
            if (btn.classList.contains('active')) return;

            toggle.disabled = true;

            fetch("{{ route('order.updateStatus', $order->order_id) }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ status })
            })
            .then(r => r.json())
            .then(data => {
                if (!data.success) throw new Error();
                renderStatus(status, label);
                showToast('تم تحديث الحالة وإشعار العميل');
            })
            .catch(() => showToast('تعذر تحديث الحالة', true))
            .finally(() => toggle.disabled = false);
        });
    });
</script>
@endsection