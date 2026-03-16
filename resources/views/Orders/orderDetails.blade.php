@extends('layouts.master')

@section('link')
    <link href="{{ asset('assets/css/order.css') }}" rel="stylesheet">
@endsection

@section('content')
    <div class="container my-4 px-2 px-md-2" dir="rtl">
        <div class="container">

            <div class="text-start">
                <a href="{{ url()->previous() }}">
                    <i class="bi bi-arrow-left fs-4 text-dark"></i>
                </a>
            </div>

            <div class="controls-container">

                <div class="section-ar" style="width: 80%">
                    <div class="section-title" style="margin-bottom: 0px;">
                        <h3 style="font-size: 20px">
                            تفاصيل <span class="orange-text">الطلبية</span>
                        </h3>
                    </div>
                </div>

                <div style="display:flex">

                    <label class="status-badge">حالة الطلب :</label>

                    <div class="order-status">

                        <span class="status-badge" id="orderStatus">
                            {{ $order->status }}
                        </span>

                        <div class="status-dropdown">

                            <button onclick="toggleStatus(this)">
                                <i class="bi bi-pencil-square"></i>
                            </button>

                            <div class="status-menu">

                                <a href="#" class="change-status" data-status="قيد المعالجة">قيد المعالجة</a>

                                <a href="#" class="change-status" data-status="تم الشحن">تم الشحن</a>

                                <a href="#" class="change-status" data-status="مكتمل">مكتمل</a>

                                <a href="#" class="change-status" data-status="ملغي">ملغي</a>

                            </div>

                        </div>
                    </div>
                </div>
            </div>


            <div class="order-box mt-3">

                <div class="order-grid">

                    <div class="order-item">
                        <label>رقم الطلب</label>
                        <span>#{{ $order->order_id }}</span>
                    </div>

                    <div class="order-item">
                        <label>تاريخ الطلب</label>
                        <span>{{ $order->created_at->format('Y-m-d') }}</span>
                    </div>

                    <div class="order-item">
                        <label>اسم العميل</label>
                        <span>{{ $order->customer->name ?? '-' }}</span>
                    </div>

                    <div class="order-item">
                        <label>رقم الجوال</label>
                        <span>{{ $order->customer->phone ?? '-' }}</span>
                    </div>

                    <div class="order-item">
                        <label>العنوان</label>
                        <span>
                            {{ $order->address->city ?? '-' }}
                        </span>
                    </div>

                    <div class="order-item">
                        <label>وسيلة الدفع</label>
                        <span>
                            {{ $order->payment->method->method_name ?? 'غير محدد' }}/
                            {{ $order->payment->status ?? 'غير محدد' }}
                        </span>
                    </div>

                </div>
            </div>


            <div class="table-container">

                <table>

                    <thead>
                        <tr>
                            <th>رقم الصنف</th>
                            <th>اسم الصنف</th>
                            <th>الكمية</th>
                            <th>السعر</th>
                            <th>الإجمالي</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach ($order->items as $item)
                            <tr>

                                <td>{{ $item->order_item_id }}</td>

                                <td>{{ $item->product->p_name }}</td>

                                <td>{{ $item->quantity }}</td>

                                <td>{{ $item->price }} ر.س</td>

                                <td>
                                    <span class="badge text-dark">
                                        {{ $item->price * $item->quantity }} ر.س
                                    </span>
                                </td>

                            </tr>
                        @endforeach

                    </tbody>

                </table>

            </div>


            <div class="order-totals mt-4">

                <div>
                    <label>إجمالي الكمية</label>

                    <span>
                        {{ $order->items->sum('quantity') }}
                    </span>

                </div>


                <div>
                    <label>إجمالي السعر</label>

                    <span class="total-price">

                        {{ $order->items->sum(function ($item) {
                            return $item->price * $item->quantity;
                        }) }}

                        ر.س

                    </span>

                </div>

            </div>

        </div>
    </div>
@endsection


@section('jsfile')
{{-- تغير حالة الطلب  --}}
    <script>
        function toggleStatus(button) {

            const dropdown = button.parentElement;

            dropdown.classList.toggle('active');

        }

        document.addEventListener('click', function(e) {

            if (!e.target.closest('.status-dropdown')) {

                document.querySelectorAll('.status-dropdown').forEach(el => {

                    el.classList.remove('active');

                });

            }

        });

        document.querySelectorAll('.change-status').forEach(function(item) {

            item.addEventListener('click', function(e) {

                e.preventDefault();

                let status = this.dataset.status;

                let dropdown = this.closest('.status-dropdown'); // القائمة

                fetch("/order/update-status/{{ $order->order_id }}", {

                        method: "POST",

                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },

                        body: JSON.stringify({
                            status: status
                        })

                    })

                    .then(response => response.json())

                    .then(data => {

                        if (data.success) {

                            // تحديث النص
                            document.getElementById('orderStatus').innerText = status;

                            // اغلاق القائمة
                            dropdown.classList.remove('active');

                        }

                    });

            });

        });
    </script>
@endsection
