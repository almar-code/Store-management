@extends('layouts.master')
@section('link')
    <link href="assets/css/order.css" rel="stylesheet">
@endsection
@section('content')
    <div class="container my-4  px-2 px-md-2" dir="rtl">
        <div class="container">

            <div class="controls-container">

                <div class="section-ar">

                    <div class="section-title" style="margin-bottom: 0px;">
                        <h3 style="font-size: 20px">قائمة <span class="orange-text">ألطلبات</span></h3>
                    </div>
                </div>
                <div class="search-input-container">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" class="search-input" placeholder="Search">
                </div>

                <a href="/orders" class="btn-add-product shadow-sm" style="justify-content: center ;" title="تحديث الصفحة">

                    <i class="bi bi-arrow-clockwise" ></i>
                </a>
            </div>

            <div class="table-container">
<<<<<<< Updated upstream
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>اسم العميل</th>
                            <th class="show">رقم العميل</th>
                            <th class="show">المدينة</th>
                            <th>إجمالي الطلب</th>
                            <th>حالة الطلب</th>
                            <th class="show">تاريخ الطلب</th>
                            <th>التفاصيل</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>سارة محمد</td>
                            <td class="show">779271679</td>
                            <td class="show">الرياض</td>
                            <td>450 ر.س</td>
                            <td><span class="badge  text-dark">قيد المعالجة</span></td>
                            <td class="show">2026-02-20</td>
                            <td>
                                <a href="/orderDetails" class="text-dark fs-5">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                         <tr>
                            <td>1</td>
                            <td>سارة محمد</td>
                            <td class="show">779271679</td>
                            <td class="show">الرياض</td>
                            <td>450 ر.س</td>
                            <td><span class="badge  text-dark">قيد المعالجة</span></td>
                            <td class="show">2026-02-20</td>
                            <td>
                                <a href="/orderDetails" class="text-dark fs-5">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                         <tr>
                            <td>1</td>
                            <td>سارة محمد</td>
                            <td class="show">779271679</td>
                            <td class="show">الرياض</td>
                            <td>450 ر.س</td>
                            <td><span class="badge  text-dark">قيد المعالجة</span></td>
                            <td class="show">2026-02-20</td>
                            <td>
                                <a href="/orderDetails" class="text-dark fs-5">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                         <tr>
                            <td>1</td>
                            <td>سارة محمد</td>
                            <td class="show">779271679</td>
                            <td class="show">الرياض</td>
                            <td>450 ر.س</td>
                            <td><span class="badge  text-dark">قيد المعالجة</span></td>
                            <td class="show">2026-02-20</td>
                            <td>
                                <a href="/orderDetails" class="text-dark fs-5">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                         <tr>
                            <td>1</td>
                            <td>سارة محمد</td>
                            <td class="show">779271679</td>
                            <td class="show">الرياض</td>
                            <td>450 ر.س</td>
                            <td><span class="badge  text-dark">قيد المعالجة</span></td>
                            <td class="show">2026-02-20</td>
                            <td>
                                <a href="#" class="text-dark fs-5">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                         <tr>
                            <td>1</td>
                            <td>سارة محمد</td>
                            <td class="show">779271679</td>
                            <td class="show">الرياض</td>
                            <td>450 ر.س</td>
                            <td><span class="badge  text-dark">قيد المعالجة</span></td>
                            <td class="show">2026-02-20</td>
                            <td>
                                <a href="/" class="text-dark fs-5">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                     
                    </tbody>
                </table>
=======
               <table>
    <thead>
        <tr>
            <th>ID</th>
            <th>اسم العميل</th>
            <th class="show">رقم العميل</th>
            <th class="show">المدينة</th>
            <th>إجمالي الطلب</th>
            <th>حالة الطلب</th>
            <th class="show">تاريخ الطلب</th>
            <th>التفاصيل</th>
        </tr>
    </thead>

    <tbody>

    @foreach ($orders as $order)

        <tr class="card-item">
            <td>{{ $order->order_id }}</td>

            <td class="card-title">{{ $order->customer->name }}</td>

            <td class="show">
                {{ $order->customer->phone ?? '-' }}
            </td>

            <td class="show">
                {{ $order->address->city ?? '-' }}
            </td>

            <td>
                {{ $order->total_price }} ر.س
            </td>

            <td>

                @if($order->status == 'قيد المعالجة')
                    <span class="badge bg-warning text-dark">قيد المعالجة</span>

                @elseif($order->status == 'مكتمل')
                    <span class="badge bg-success">مكتمل</span>

                @elseif($order->status == 'ملغي')
                    <span class="badge bg-danger" >ملغي</span>

                @else
                    <span class="badge bg-secondary">{{ $order->status }}</span>

                @endif

            </td>

            <td class="show">
                {{ $order->created_at->format('Y-m-d') }}
            </td>

            <td>
                <a href="/orderDetails/{{ $order->order_id }}" class="text-dark fs-5">
                    <i class="bi bi-eye" title="عرض تفاصيل الطلب"></i>
                </a>
            </td>

        </tr>

    @endforeach

    </tbody>
</table>
>>>>>>> Stashed changes
            </div>
        </div>
    </div>
@endsection
