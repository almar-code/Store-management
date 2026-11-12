@extends('Layouts.master')

@section('link')
    <link href="{{ asset('assets/css/orders-premium.css') }}?v={{ time() }}" rel="stylesheet">
    <link href="{{ secure_asset('assets/css/orders-premium.css') }}?v={{ time() }}" rel="stylesheet">

    <style>
        /* ===== طريقة الدفع ===== */
        .pm-method { display: flex; align-items: center; gap: 10px; }

        .pm-method .ic {
            width: 38px;
            height: 38px;
            flex-shrink: 0;
            border-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
        }

        .ic-cash          { background: #fff4ec; color: #e16b38; }
        .ic-wallet       { background: #eaf2ff; color: #2f6fed; }
        .ic-bank_account { background: #f1f8f6; color: #0f766e; }
        .ic-bank_transfer { background: #eef0ff; color: #4f46e5; }
        .ic-stripe        { background: #e6f5f2; color: #008870; }

        .pm-method small, .pm-sub {
            display: block;
            color: var(--op-muted);
            font-size: 12px;
        }

        /* ===== حالات المراجعة ===== */
        .rv-pending   { background: #fff4ec; color: #e16b38; }
        .rv-confirmed { background: #e6f5f2; color: #008870; }
        .rv-rejected  { background: #fdecec; color: #e53935; }

        /* ===== أزرار الإجراء ===== */
        .pm-actions { display: flex; gap: 6px; }

        .pm-act {
            height: 36px;
            padding: 0 12px;
            border-radius: 10px;
            border: 1px solid var(--op-border);
            background: #fff;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all .2s ease;
            white-space: nowrap;
        }

        .pm-act.ok { color: #008870; }
        .pm-act.no { color: #e53935; }
        .pm-act.ok:hover, .pm-act.ok.on { background: #008870; border-color: #008870; color: #fff; }
        .pm-act.no:hover, .pm-act.no.on { background: #e53935; border-color: #e53935; color: #fff; }

        .pm-select {
            height: 40px;
            padding: 0 12px;
            border: 1px solid var(--op-border);
            border-radius: 12px;
            background: #fff;
            font-size: 14px;
            outline: none;
        }

        .pm-select:focus { border-color: var(--op-accent); }

        /* ===== نافذة التأكيد ===== */
        .pm-modal {
            position: fixed;
            inset: 0;
            z-index: 1500;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
            background: rgba(17, 24, 39, .45);
            backdrop-filter: blur(4px);
            opacity: 0;
            visibility: hidden;
            transition: all .25s ease;
        }

        .pm-modal.open { opacity: 1; visibility: visible; }

        .pm-box {
            width: 100%;
            max-width: 440px;
            padding: 24px;
            background: #fff;
            border-radius: 22px;
            box-shadow: 0 24px 60px rgba(0, 0, 0, .2);
            transform: translateY(16px) scale(.97);
            transition: transform .25s ease;
            direction: rtl;
        }

        .pm-modal.open .pm-box { transform: none; }

        .pm-head-ic {
            width: 60px;
            height: 60px;
            margin: 0 auto 12px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
        }

        .pm-box.confirm .pm-head-ic { background: #e6f5f2; color: #008870; }
        .pm-box.reject .pm-head-ic  { background: #fdecec; color: #e53935; }

        .pm-box h5 { text-align: center; font-weight: 700; margin-bottom: 4px; }
        .pm-lead { text-align: center; color: var(--op-muted); font-size: 13.5px; margin-bottom: 16px; }

        .pm-summary {
            padding: 6px 14px;
            margin-bottom: 14px;
            background: var(--op-soft);
            border-radius: 14px;
        }

        .pm-box textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--op-border);
            border-radius: 12px;
            font-size: 14px;
            resize: none;
            outline: none;
        }

        .pm-box textarea:focus {
            border-color: var(--op-accent);
            box-shadow: 0 0 0 3px rgba(0, 136, 112, .12);
        }

        .pm-btns { display: flex; gap: 10px; margin-top: 16px; }
        .pm-btns .op-btn { flex: 1; }

        .op-btn.danger {
            background: #e53935;
            border-color: #e53935;
            color: #fff;
            box-shadow: 0 6px 14px rgba(229, 57, 53, .25);
        }

        .op-btn.danger:hover { color: #fff; filter: brightness(1.06); }

        @media (max-width: 767px) {
            .pm-select { flex: 1; }
            .pm-actions { justify-content: flex-end; }
        }
    </style>
@endsection

@section('content')
<div class="container my-4 px-2 px-md-3 op-page">

    {{-- رأس الصفحة --}}
    <div class="op-head">
        <div>
            <h3 class="op-title">مراجعة <span>المدفوعات</span></h3>
            <div class="op-sub">تأكد من وصول الحوالات والإيداعات ثم حدّث حالة الدفع</div>
        </div>

        <div class="op-actions">
            <div class="op-search">
                <i class="bi bi-search"></i>
                <input type="text" id="paySearch" placeholder="رقم الطلب، العميل أو رقم العملية">
            </div>
            <select class="pm-select" id="methodFilter">
                <option value="all">كل طرق الدفع</option>
                @foreach(\App\Models\Payment::PROVIDERS as $key => $p)
                    <option value="{{ $key }}">{{ $p['label'] }}</option>
                @endforeach
            </select>
            <a href="/payments" class="op-btn" title="تحديث"><i class="bi bi-arrow-clockwise"></i></a>
        </div>
    </div>

    {{-- الإحصائيات --}}
    <div class="op-stats">
        <div class="op-stat">
            <div class="icon" style="background:#fff4ec; color:#e16b38"><i class="bi bi-hourglass-split"></i></div>
            <div><span class="value" id="statPending">0</span><span class="label">بانتظار المراجعة</span></div>
        </div>
        <div class="op-stat">
            <div class="icon"><i class="bi bi-cash-stack"></i></div>
            <div><span class="value" id="statPendingAmount">0</span><span class="label">مبالغ معلقة (ر.س)</span></div>
        </div>
        <div class="op-stat">
            <div class="icon"><i class="bi bi-check2-circle"></i></div>
            <div><span class="value" id="statConfirmed">0</span><span class="label">تم استلامها</span></div>
        </div>
        <div class="op-stat">
            <div class="icon" style="background:#fdecec; color:#e53935"><i class="bi bi-x-circle"></i></div>
            <div><span class="value" id="statRejected">0</span><span class="label">مرفوضة</span></div>
        </div>
    </div>

    {{-- التبويبات --}}
    <div class="op-tabs">
        <button class="op-tab active" data-filter="pending">بانتظار المراجعة <span class="count" data-count="pending">0</span></button>
        <button class="op-tab" data-filter="confirmed">تم الاستلام <span class="count" data-count="confirmed">0</span></button>
        <button class="op-tab" data-filter="rejected">مرفوضة <span class="count" data-count="rejected">0</span></button>
        <button class="op-tab" data-filter="all">الكل <span class="count" data-count="all">0</span></button>
    </div>

    {{-- الجدول --}}
    <div class="op-card op-table-card">
        <table class="op-table" id="paymentsTable">
            <thead>
                <tr>
                    <th>العميل</th>
                    <th>الطلب</th>
                    <th>طريقة الدفع</th>
                    <th>رقم العملية</th>
                    <th>المبلغ</th>
                    <th>الحالة</th>
                    <th>الإجراء</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $payment)
                    @php
                        $order = $payment->order;
                        $customer = $order?->customer;
                        $name = $customer->name ?? 'عميل';
                        $info = $payment->providerInfo();
                        $state = $payment->reviewState();
                        $ref = $payment->customer_transaction_reference ?: ($payment->transaction_id ?: '-');
                        $search = mb_strtolower($payment->order_id . ' ' . $name . ' ' . ($customer->phone ?? '') . ' ' . $ref);
                    @endphp
                    <tr data-id="{{ $payment->payment_id }}"
                        data-state="{{ $state }}"
                        data-method="{{ $payment->methodKey() }}"
                        data-amount="{{ $payment->amount }}"
                        data-search="{{ $search }}"
                        data-order="#{{ $payment->order_id }}"
                        data-customer="{{ $name }}"
                        data-method-label="{{ $info['label'] }}"
                        data-ref="{{ $ref }}">

                        <td class="op-td-main">
                            <div class="op-customer">
                                <div class="op-avatar">{{ mb_substr($name, 0, 1) }}</div>
                                <div>
                                    <strong>{{ $name }}</strong>
                                    <small>{{ $customer->phone ?? '-' }}</small>
                                </div>
                            </div>
                        </td>

                        <td data-label="الطلب">
                            <div>
                                <a href="/orderDetails/{{ $payment->order_id }}" class="op-id text-decoration-none">#{{ $payment->order_id }}</a>
                                <span class="pm-sub">{{ $payment->created_at->format('Y-m-d') }}</span>
                            </div>
                        </td>

                        <td data-label="طريقة الدفع">
    <div class="pm-method">
        <div class="ic ic-{{ $payment->methodKey() }}"><i class="bi {{ $info['icon'] }}"></i></div>
        <div>
            {{ $info['label'] }}
            @if($payment->store_account_name)
                <small>{{ $payment->store_account_name }} • {{ $payment->store_account_number }}</small>
            @endif
        </div>
    </div>
</td>
                        <td data-label="رقم العملية">
                            <div>
                                {{ $ref }}
                                @if($payment->customer_bank_name)
                                    <span class="pm-sub">{{ $payment->customer_bank_name }}</span>
                                @endif
                            </div>
                        </td>

                        <td data-label="المبلغ"><strong>{{ number_format($payment->amount, 2) }} ر.س</strong></td>

                        <td data-label="الحالة">
                            <div>
                                <span class="op-status rv-{{ $state }}" data-badge>{{ \App\Models\Payment::REVIEW_STATES[$state] }}</span>
                                <span class="pm-sub" data-verified>
                                    @if($payment->verified_at)
                                        {{ $payment->verifier->full_name ?? '' }} • {{ $payment->verified_at->format('Y-m-d h:i A') }}
                                    @endif
                                </span>
                            </div>
                        </td>

                        <td data-label="الإجراء">
                            <div class="pm-actions">
                                <button type="button" class="pm-act ok {{ $state === 'confirmed' ? 'on' : '' }}" data-action="confirmed">
                                    <i class="bi bi-check-lg"></i> تم الاستلام
                                </button>
                                <button type="button" class="pm-act no {{ $state === 'rejected' ? 'on' : '' }}" data-action="rejected">
                                    <i class="bi bi-x-lg"></i> رفض
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="op-empty" id="payEmpty" hidden>
            <i class="bi bi-inbox"></i>
            لا توجد مدفوعات هنا
        </div>
    </div>
</div>

{{-- نافذة التأكيد --}}
<div class="pm-modal" id="pmModal">
    <div class="pm-box confirm" id="pmBox">
        <div class="pm-head-ic"><i class="bi bi-check2-circle" id="pmIcon"></i></div>
        <h5 id="pmTitle"></h5>
        <p class="pm-lead" id="pmLead"></p>

        <div class="pm-summary">
            <div class="op-info"><span>الطلب</span><span id="pmOrder"></span></div>
            <div class="op-info"><span>العميل</span><span id="pmCustomer"></span></div>
            <div class="op-info"><span>طريقة الدفع</span><span id="pmMethod"></span></div>
            <div class="op-info"><span>رقم العملية</span><span id="pmRef"></span></div>
            <div class="op-info op-total"><span>المبلغ</span><span id="pmAmount"></span></div>
        </div>

        <textarea id="pmNote" rows="2" placeholder="ملاحظة (اختياري)"></textarea>

        <div class="pm-btns">
            <button type="button" class="op-btn" id="pmCancel">إلغاء</button>
            <button type="button" class="op-btn primary" id="pmSubmit"></button>
        </div>
    </div>
</div>

<div class="op-toast" id="opToast"></div>
@endsection

@section('jsfile')
<script>
    const rows = [...document.querySelectorAll('#paymentsTable tbody tr')];
    const search = document.getElementById('paySearch');
    const methodFilter = document.getElementById('methodFilter');
    const empty = document.getElementById('payEmpty');
    let activeTab = 'pending';

    // ===== التصفية =====
    function applyFilter() {
        const q = search.value.trim().toLowerCase();
        const method = methodFilter.value;
        let visible = 0;

        rows.forEach(row => {
            const show = (activeTab === 'all' || row.dataset.state === activeTab)
                && (method === 'all' || row.dataset.method === method)
                && row.dataset.search.includes(q);
            row.hidden = !show;
            if (show) visible++;
        });

        empty.hidden = visible > 0;
    }

    // ===== الإحصائيات من الصفوف =====
    function refreshStats() {
        const count = s => rows.filter(r => r.dataset.state === s).length;
        const pendingAmount = rows
            .filter(r => r.dataset.state === 'pending')
            .reduce((sum, r) => sum + parseFloat(r.dataset.amount || 0), 0);

        document.getElementById('statPending').textContent = count('pending');
        document.getElementById('statConfirmed').textContent = count('confirmed');
        document.getElementById('statRejected').textContent = count('rejected');
        document.getElementById('statPendingAmount').textContent =
            pendingAmount.toLocaleString('en', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        document.querySelectorAll('[data-count]').forEach(el => {
            const s = el.dataset.count;
            el.textContent = s === 'all' ? rows.length : count(s);
        });
    }

    search.addEventListener('input', applyFilter);
    methodFilter.addEventListener('change', applyFilter);

    document.querySelectorAll('.op-tab').forEach(tab => {
        tab.addEventListener('click', () => {
            document.querySelectorAll('.op-tab').forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            activeTab = tab.dataset.filter;
            applyFilter();
        });
    });

    // ===== نافذة التأكيد =====
    const modal = document.getElementById('pmModal');
    const box = document.getElementById('pmBox');
    const submitBtn = document.getElementById('pmSubmit');
    let current = { row: null, action: null };

    function openModal(row, action) {
        if (row.dataset.state === action) return;
        current = { row, action };
        const isConfirm = action === 'confirmed';

        box.className = 'pm-box ' + (isConfirm ? 'confirm' : 'reject');
        document.getElementById('pmIcon').className = 'bi ' + (isConfirm ? 'bi-check2-circle' : 'bi-x-octagon');
        document.getElementById('pmTitle').textContent = isConfirm ? 'تأكيد استلام المبلغ' : 'رفض عملية الدفع';
        document.getElementById('pmLead').textContent = isConfirm
            ? 'تأكد أن المبلغ وصل فعلاً إلى حساب المتجر قبل التأكيد'
            : 'سيتم تغيير حالة دفع الطلب إلى فشل الدفع';

        document.getElementById('pmOrder').textContent = row.dataset.order;
        document.getElementById('pmCustomer').textContent = row.dataset.customer;
        document.getElementById('pmMethod').textContent = row.dataset.methodLabel;
        document.getElementById('pmRef').textContent = row.dataset.ref;
        document.getElementById('pmAmount').textContent = parseFloat(row.dataset.amount).toFixed(2) + ' ر.س';
        document.getElementById('pmNote').value = '';

        submitBtn.className = 'op-btn ' + (isConfirm ? 'primary' : 'danger');
        submitBtn.textContent = isConfirm ? 'نعم، تم الاستلام' : 'نعم، رفض';

        modal.classList.add('open');
    }

    function closeModal() {
        modal.classList.remove('open');
    }

    document.getElementById('pmCancel').addEventListener('click', closeModal);
    modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

    document.querySelectorAll('[data-action]').forEach(btn => {
        btn.addEventListener('click', () => openModal(btn.closest('tr'), btn.dataset.action));
    });

    // ===== رسالة سريعة =====
    function showToast(text, isError = false) {
        const toast = document.getElementById('opToast');
        toast.textContent = text;
        toast.classList.toggle('error', isError);
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 2500);
    }

    // ===== الارسال عبر AJAX =====
    submitBtn.addEventListener('click', () => {
        const { row, action } = current;
        submitBtn.disabled = true;

        fetch(`/payments/${row.dataset.id}/verify`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                status: action,
                note: document.getElementById('pmNote').value
            })
        })
        .then(r => r.json())
        .then(data => {
            if (!data.success) throw new Error();

            // تحديث السطر بدون اعادة تحميل
            row.dataset.state = data.state;
            const badge = row.querySelector('[data-badge]');
            badge.className = 'op-status rv-' + data.state;
            badge.textContent = data.label;
            row.querySelector('[data-verified]').textContent = `${data.verified_by} • ${data.verified_at}`;
            row.querySelectorAll('[data-action]').forEach(b =>
                b.classList.toggle('on', b.dataset.action === data.state));

            closeModal();
            refreshStats();
            applyFilter();
            showToast(data.state === 'confirmed' ? 'تم تأكيد استلام المبلغ' : 'تم رفض عملية الدفع');
        })
        .catch(() => showToast('تعذر تحديث حالة الدفع', true))
        .finally(() => submitBtn.disabled = false);
    });

    refreshStats();
    applyFilter();
</script>
@endsection