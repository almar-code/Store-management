@extends('layouts.master')

@section('link')
    <link href="{{ asset('assets/css/order.css') }}" rel="stylesheet">
@endsection

@section('content')
    <div class="container my-4 px-2 px-md-2" dir="rtl">
        <div class="container">

            <!-- 🔹 هذا الجزء كما طلبت بدون أي تعديل -->
            <div class="controls-container">
                <div class="section-ar" style="width: 80%">
                    <div class="section-title" style="margin-bottom: 0px;">
                        <h3 style="font-size: 20px">
                            تفاصيل <span class="orange-text">الطلبية</span>
                        </h3>
                    </div>
                </div>

                <div style="display: flex">
                    <label class="status-badge">حالة الطلب : </label>
                    <div class="order-status">

                        <span class="status-badge">قيد المعالجة</span>

                        <div class="status-dropdown">
                            <button onclick="toggleStatus(this)">
                                <i class="bi bi-pencil-square"></i>
                            </button>

                            <div class="status-menu">
                                <a href="#">قيد المعالجة</a>
                                <a href="#">تم الشحن</a>
                                <a href="#">مكتمل</a>
                                <a href="#">ملغي</a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="order-box mt-3">
                <div class="order-grid">

                    <div class="order-item">
                        <label>رقم الطلب</label>
                        <span>#1025</span>
                    </div>

                    <div class="order-item">
                        <label>تاريخ الطلب</label>
                        <span>2026-03-01</span>
                    </div>

                    <div class="order-item">
                        <label>اسم العميل</label>
                        <span>سارة محمد</span>
                    </div>

                    <div class="order-item">
                        <label>رقم الجوال</label>
                        <span>0591234567</span>
                    </div>

                    <div class="order-item">
                        <label>العنوان</label>
                        <span>الرياض - حي الياسمين</span>
                    </div>

                    <div class="order-item">
                        <label>وسيلة الدفع</label>
                        <span>بطاقة مدى</span>
                    </div>

                </div>
            </div>
            <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>اسم الصنف</th>
                                <th>اللون</th>
                                <th>المقاس</th>
                                <th>الكمية</th>
                                <th>اجمالي السعر</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>عباية رسمية</td>
                                <td>الاسود</td>
                                <td>52</td>
                                <td>2</td>
                                <td><span class="badge text-dark">200</span></td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>عباية رسمية</td>
                                <td>الاسود</td>
                                <td>52</td>
                                <td>2</td>
                                <td><span class="badge text-dark">200</span></td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>عباية رسمية</td>
                                <td>الاسود</td>
                                <td>52</td>
                                <td>2</td>
                                <td><span class="badge text-dark">200</span></td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>عباية رسمية</td>
                                <td>الاسود</td>
                                <td>52</td>
                                <td>2</td>
                                <td><span class="badge text-dark">200</span></td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>عباية رسمية</td>
                                <td>الاسود</td>
                                <td>52</td>
                                <td>2</td>
                                <td><span class="badge text-dark">200</span></td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>عباية رسمية</td>
                                <td>الاسود</td>
                                <td>52</td>
                                <td>2</td>
                                <td><span class="badge text-dark">200</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="order-totals mt-4">
                    <div>
                        <label>إجمالي الكمية</label>
                        <span>10</span>
                    </div>

                    <div>
                        <label>إجمالي السعر</label>
                        <span class="total-price">1000 ر.س</span>
                    </div>
                </div>

            </div>
        </div>
    @endsection


    @section('jsfile')
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
        </script>
    @endsection
