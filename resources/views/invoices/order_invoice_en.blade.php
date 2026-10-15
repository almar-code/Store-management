<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Order Invoice #{{ $order->order_id }}</title>

    <style>
        @page {
            margin: 25px;
        }

        @font-face {
            font-family: 'DejaVu Sans';
            font-style: normal;
            font-weight: normal;
            src: url('{{ storage_path('fonts/DejaVuSans.ttf') }}') format('truetype');
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #222222;
            font-size: 13px;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
            direction: ltr;
            text-align: left;
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
        }

        .header-table td {
            border: none;
            padding: 4px;
            vertical-align: middle;
        }

        .brand-cell {
            width: 60%;
            text-align: left;
        }

        .logo {
            width: 40px;
            height: 40px;
            vertical-align: middle;
        }

        .brand-name {
            display: inline-block;
            vertical-align: middle;
            font-size: 27px;
            font-weight: bold;
        }

        .nice-n {
            color: #008870;
        }

        .nice-ice {
            color: #000000;
        }

        .invoice-cell {
            width: 40%;
            text-align: right;
        }

        .invoice-title {
            font-size: 22px;
            color: #111111;
            font-weight: bold;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #037862;
            margin-top: 20px;
            margin-bottom: 8px;
            padding-bottom: 4px;
            text-align: left;
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
            font-weight: bold;
            color: #333;
            text-align: left;
        }

        .value {
            width: 65%;
            text-align: left;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 20px;
        }

        .items-table th {
            background-color: #037862;
            color: #ffffff;
            padding: 10px;
            text-align: left;
            border: 1px solid #037862;
            font-size: 13px;
        }

        .items-table td {
            padding: 8px 10px;
            border: 1px solid #e0e0e0;
            font-size: 12px;
            text-align: left;
        }

        .total-row td {
            font-size: 13px;
            color: #037862;
            border-top: 2px solid #037862;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            color: #666666;
            font-size: 15px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>

<body>

    @php
        $translateOrderStatus = function($status) {
            $status = strtolower(trim($status));
            $map = [
                'pending' => 'Pending',
                'processing' => 'Processing',
                'completed' => 'Completed',
                'cancelled' => 'Cancelled',
                'shipped' => 'Shipped',
                'ready' => 'Ready',
            ];
            return $map[$status] ?? ucfirst($status);
        };

        $translatePaymentStatus = function($status) {
            $status = strtolower(trim($status));
            $map = [
                'pending' => 'Pending',
                'paid' => 'Paid',
                'failed' => 'Payment Failed',
                'refunded' => 'Refunded',
            ];
            return $map[$status] ?? ucfirst($status);
        };
    @endphp

    <div class="header">
        <table class="header-table">
            <tr>
                <td class="brand-cell">
                    <img src="{{ public_path('assets/img/logo.png') }}" class="logo" alt="Nice Logo">
                    <span class="brand-name">
                        <span class="nice-n">N</span><span class="nice-ice">ice Store</span>
                    </span>
                </td>
                <td class="invoice-cell">
                    <div class="invoice-title">
                        Order Invoice <span style="color: #037862;">{{ $order->order_id }}</span>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Order ID:</td>
            <td class="value">{{ $order->order_id }}</td>
            <td class="label" colspan="2">Order Date:</td>
            <td class="value">{{ $order->created_at ? $order->created_at->format('Y-m-d H:i') : '-' }}</td>
        </tr>
        <tr>
            <td class="label" colspan="2">Order Status:</td>
            <td class="value">{{ $translateOrderStatus($order->status) }}</td>
            <td class="label" colspan="2">Payment Status:</td>
            <td class="value">{{ $translatePaymentStatus($order->payment_status) }}</td>
        </tr>
        @if ($order->address)
            <tr>
                <td class="label"> Address:</td>
                <td class="value" colspan="3">
                    {{ ($order->address->street ?? '') . ' / ' . ($order->address->city ?? '') . ' / ' . ($order->address->country ?? '') }}
                </td>
            </tr>
        @endif
    </table>

    <div class="section-title">Order Details</div>
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 10%; text-align: center;">ID</th>
                <th style="width: 45%;">Product Name</th>
                <th style="width: 15%; text-align: center;">Quantity</th>
                <th style="width: 15%; text-align: center;">Price</th>
                <th style="width: 15%; text-align: center;">Total</th>
            </tr>
        </thead>
        <tbody>
            @if ($order->orderItems && $order->orderItems->count() > 0)
                @foreach ($order->orderItems as $index => $item)
                    <tr>
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td>{{ $item->product->p_name_en ?? 'Product ' . $item->p_id }}</td>
                        <td style="text-align: center;">{{ $item->quantity }}</td>
                        <td style="text-align: center;">${{ number_format((float) $item->price, 2) }}</td>
                        <td style="text-align: center;">${{ number_format((float) ($item->price * $item->quantity), 2) }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="5" style="text-align: center; color: #777; padding: 15px;">
                        No products registered for this order.
                    </td>
                </tr>
            @endif

            <tr class="total-row">
                <td colspan="4" style="text-align: right;">Total Amount:</td>
                <td style="text-align: center;">${{ number_format((float) $order->total_price, 2) }}</td>
            </tr>
            <tr>
                <td colspan="4" style="text-align: right; font-weight: bold;">Total Items Count:</td>
                <td style="text-align: center; font-weight: bold;">{{ $order->orderItems ? $order->orderItems->count() : 0 }}</td>
            </tr>
            <tr>
                <td colspan="4" style="text-align: right; font-weight: bold;">Total Quantities:</td>
                <td style="text-align: center; font-weight: bold;">{{ $order->orderItems ? $order->orderItems->sum('quantity') : 0 }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Thank you for your trust in us!
    </div>

</body>

</html>