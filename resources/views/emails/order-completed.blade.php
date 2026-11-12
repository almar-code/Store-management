<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طلبك جاهز</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f6f8; font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; direction:rtl;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:#f4f6f8; padding:20px 0;">
    <tr>
        <td align="center">
            <table role="presentation" width="100%" style="max-width:550px; background-color:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 4px 15px rgba(0,0,0,0.05);">

                {{-- الهيدر --}}
                <tr>
                    <td style="background-color:#111827; padding:20px; text-align:center;">
                        <h1 style="color:#ffffff; margin:0; font-size:24px; letter-spacing:1px;">NICE</h1>
                        <p style="color:#9ca3af; margin:4px 0 0; font-size:12px;">SMART STORE</p>
                    </td>
                </tr>

                {{-- الرسالة --}}
                <tr>
                    <td style="padding:28px 24px 8px; text-align:center;">
                        <div style="width:64px; height:64px; line-height:64px; border-radius:50%; background-color:#e6faf3; color:#00B387; font-size:30px; margin:0 auto 14px;">&#10003;</div>
                        <h2 style="margin:0 0 8px; color:#111827; font-size:20px;">
                            مرحبا {{ $customer->name ?? 'عميلنا العزيز' }}
                        </h2>
                            <p style="margin:0; color:#6b7280; font-size:14px; line-height:1.7;">
                            طلبك أصبح جاهزاً وسيتم تجهيزه للشحن قريباً، شكرا لثقتك بمتجر NICE.
                        </p>
                    </td>
                </tr>

                {{-- ملخص الطلب --}}
                <tr>
                    <td style="padding:16px 24px;">
                        <table role="presentation" width="100%" style="border:1px solid #eef0f3; border-radius:12px;">
                            <tr>
                                <td style="padding:12px; color:#6b7280; font-size:13px;">رقم الطلب</td>
                                <td style="padding:12px; color:#111827; font-weight:bold; text-align:left;">#{{ $order->order_id }}</td>
                            </tr>
                            <tr>
                                <td style="padding:12px; color:#6b7280; font-size:13px; border-top:1px solid #eef0f3;">الإجمالي</td>
                                <td style="padding:12px; color:#00B387; font-weight:bold; text-align:left; border-top:1px solid #eef0f3;">{{ $order->total_price }} ر.س</td>
                            </tr>
                        </table>
                    </td>
                </tr>

                {{-- الزر --}}
                <tr>
                    <td style="padding:8px 24px 28px; text-align:center;">
                        <a href="{{ $appUrl }}" style="background-color:#00B387; color:#ffffff; text-decoration:none; padding:12px 32px; border-radius:12px; font-weight:bold; font-size:15px; display:inline-block;">
                            تابع طلبك
                        </a>
                    </td>
                </tr>

                {{-- الفوتر --}}
                <tr>
                    <td style="background-color:#f9fafb; padding:14px; text-align:center; color:#9ca3af; font-size:11px;">
                        متجر NICE &copy; {{ date('Y') }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>