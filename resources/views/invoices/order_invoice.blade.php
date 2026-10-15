@php
    // دالة لترجمة حالة الطلب
    $translateOrderStatus = function($status) use ($arabicText) {
        $status = strtolower(trim($status));
        $map = [
            'pending' => 'قيد الانتظار',
            'processing' => 'قيد المعالجة',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
            'shipped' => 'تم الشحن',
            'ready' => 'جاهز ',
        ];
        return $arabicText($map[$status] ?? ucfirst($status));
    };

    // دالة لترجمة حالة الدفع
    $translatePaymentStatus = function($status) use ($arabicText) {
        $status = strtolower(trim($status));
        $map = [
            'pending' => 'معلق / غير مدفوع',
            'paid' => 'مدفوع',
            'failed' => 'فشل الدفع',
            'refunded' => 'مسترد',
        ];
        return $arabicText($map[$status] ?? ucfirst($status));
    };
@endphp
<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <title>فاتورة الطلب رقم {{ $order->order_id }}</title>

    <style>
        @page {
            margin: 25px;
        }

        @font-face {
            font-family: 'DejaVu Sans';
            font-style: normal;
            font-weight: normal;
            src: url('{{ storage_path('fonts/Parastoo.ttf') }}') format('truetype');
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #222222;
            font-size: 13px;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }

       .header {
    width: 100%;
    border-bottom: 2px solid #037862;
    padding-bottom: 12px;
    margin-bottom: 25px;
}

.header-table {
    width: 100%;
    border-collapse: collapse;
    direction: rtl;
}

.header-table td {
    border: none;
    padding: 4px;
    vertical-align: middle;
}

.brand-cell {
    width: 70%;
    text-align: left;
    white-space: nowrap;
}

.logo {
    width: 40px;
    height: 40px;
    vertical-align: middle;
}

.brand-name {
    display: inline-block;
    vertical-align: middle;
    font-family: 'DejaVu Sans', sans-serif;
    font-size: 27px;
    font-weight: bold;
    direction: ltr;
    unicode-bidi: embed;
}

.nice-n {
    color: #008870;
}

.nice-ice {
    color: #000000;
}

.invoice-cell {
    width: 30%;
    text-align: right;
    direction: rtl;
}

.invoice-title {
    font-size: 25px;
    color: #111111;
    text-align: right;
    direction: rtl;
}

        .section-title {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 18px;
            color: #037862;
            margin-top: 20px;
            margin-bottom: 8px;
            padding-bottom: 4px;
            text-align: right;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .info-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #eeeeee;
            vertical-align: middle;
        }

        .label {
            width: 35%;
            background-color: #ffffff;
            color: #333;
            text-align: right;
        }

        .value {
            width: 65%;
            text-align: right;
        }

        /* جدول المنتجات */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 20px;
        }

        .items-table th {
           font-family: 'DejaVu Sans', sans-serif;
            font-size: 13px;
            background-color: #037862;
            color: #ffffff;
            padding: 10px;
            border: 1px solid #037862;
        }

        .items-table td {
            padding: 8px 10px;
            border: 1px solid #e0e0e0;
            font-size: 12px;
            text-align: right;
        }

        .items-table tr:nth-child(even) {
            background-color: #fffff;
        }

        .total-row td {
            font-size: 13px;
            color: #037862;
            background-color: #ffffff;
            border-top: 2px solid #037862;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            color: #666666;
            font-size: 17px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>

<body>

    <!-- الهيدر مرتب ومنفصل تماماً لمنع الانعكاس -->
 <div class="header">
    <table class="header-table">
        <tr>
            
            <td class="brand-cell">
                <img
                    src="{{ public_path('assets/img/logo.png') }}"
                    class="logo"
                    alt="Nice Logo"
                >

                <span class="brand-name">
                    <span class="nice-n">N</span><span class="nice-ice">ice Store</span>
                </span>
            </td>
             <td class="invoice-cell">
                <div class="invoice-title">
                   
                    <span dir="ltr" style=" color: #037862;">{{ $order->order_id }}</span>
                     {{ $arabicText('فاتورة الطلب') }}
                </div>
            </td>
        </tr>
    </table>
</div>
    <!-- معلومات الطلب -->
    <table class="info-table">
        <tr>
            <td class="value" dir="ltr" style="text-align: right;">
                {{ $order->created_at ? $order->created_at->format('Y-m-d H:i') : '-' }}
            </td>
            <td class="label">{{ $arabicText('تاريخ الطلب :') }}</td>
            <td class="value" dir="ltr" style="text-align: right;">{{ $order->order_id }}</td>

            <td class="label">{{ $arabicText('رقم الطلب :') }}</td>


        </tr>
        <tr>
            <td class="value">{{ $translatePaymentStatus($order->payment_status) }}</td>
            <td class="label">{{ $arabicText('حالة الدفع :') }}</td>
            
            <td class="value">{{ $translateOrderStatus($order->status) }}</td>
            <td class="label">{{ $arabicText('حالة الطلب :') }}</td>
        </tr>
        @if ($order->address)
            <tr>
                <td class="value" colspan="3">
                    {{ $arabicText(($order->address->country ?? '') . ' / ' . ($order->address->city ?? '') . ' / ' . ($order->address->title ?? '')) }}
                </td>
                <td class="label">{{ $arabicText('عنوان التوصيل :') }}</td>

            </tr>
        @endif
    </table>

    <!-- جدول المنتجات -->
    <div class="section-title">{{ $arabicText('تفاصيل الطلب ') }}</div>
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 15%; text-align: center;">{{ $arabicText('الإجمالي') }}</th>
                <th style="width: 15%; text-align: center;">{{ $arabicText('السعر') }}</th>
                <th style="width: 15%; text-align: center;">{{ $arabicText('الكمية') }}</th>
                <th style="width: 40%;">{{ $arabicText('اسم المنتج') }}</th>
                <th style="width: 15%; text-align: center;"> {{ $arabicText('رقم الصنف') }}</th>
            </tr>
        </thead>
        <tbody>
            @if ($order->orderItems && $order->orderItems->count() > 0)
                @foreach ($order->orderItems as $index => $item)
                    <tr>
                        
                        <td style="text-align: center;" dir="ltr">
                            ${{ number_format((float) ($item->price * $item->quantity), 2) }}</td>
                            <td style="text-align: center;" dir="ltr">${{ number_format((float) $item->price, 2) }}
                        </td>
                        <td style="text-align: center;" dir="ltr">{{ $item->quantity }}</td>

                        <td>{{ $arabicText($item->product->p_name ?? 'منتج #' . $item->p_id) }}</td>

                        <td style="text-align: center;">{{ $index + 1 }}</td>

                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="5" style="text-align: center; color: #777; padding: 15px;">
                        {{ $arabicText('لا توجد منتجات مسجلة لهذا الطلب') }}
                    </td>
                </tr>
            @endif

            <!-- الإجمالي الكلي -->
            <tr class="total-row">
                
                <td dir="ltr" style="text-align: center;">
                    ${{ number_format((float) $order->total_price, 2) }}
                </td>
                <td colspan="4" style="text-align: left; background-color: #ffffff; color: #333333;">
                    {{ $arabicText('الإجمالي الكلي') }}
                </td>
                <tr>
               
                <td style="text-align: center; font-weight: bold; background-color: #ffffff;" dir="ltr">
                    {{ $order->orderItems ? $order->orderItems->count() : 0 }}
                </td>
                 <td colspan="4" style="text-align: left;background-color: #ffffff; color: #333333;">
                    {{ $arabicText('عدد الأصناف') }}
                </td>
            </tr>

            <!-- إجمالي الكمية -->
            <tr>
                 <td style="text-align: center; font-weight: bold; background-color: #ffffff;" dir="ltr">
                    {{ $order->orderItems ? $order->orderItems->sum('quantity') : 0 }}
                </td>
                <td colspan="4" style="text-align: left; background-color: #ffffff; color: #333333;">
                    {{ $arabicText('إجمالي الكمية') }}
                </td>
               
            </tr>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        {{ $arabicText('شكراً لثقتكم بنا') }}
    </div>

</body>

</html>
