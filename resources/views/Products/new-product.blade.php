<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->p_name }}</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f6f8; font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; direction:rtl;">

    <!-- Preheader Text (معاينة النص بجانب العنوان) -->
    <span style="display:none !important; visibility:hidden; mso-hide:all; font-size:1px; color:#ffffff; line-height:1px; max-height:0px; max-width:0px; opacity:0; overflow:hidden;">
        🌌 وصل حديثًا: {{ $product->p_name }} | تشكيلة جديدة ومميزة متوفرة الآن في متجر NICE!
    </span>

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:#f4f6f8; padding: 20px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" style="max-width:550px; background-color:#ffffff; border-radius:16px; overflow:hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                    
                    <!-- الهيدر -->
                    <tr>
                        <td style="background-color:#111827; padding:20px; text-align:center;">
                            <h1 style="color:#ffffff; margin:0; font-size:24px; letter-spacing:1px; font-weight:bold;">NICE</h1>
                            <p style="color:#9ca3af; margin:4px 0 0 0; font-size:12px;">SMART STORE</p>
                        </td>
                    </tr>

                    <!-- صورة المنتج -->
                    @if(!empty($product->p_image))
                    <tr>
                        <td style="padding:0; text-align:center;">
                            <img src="{{ asset('storage/uploads/products/' . $product->p_image) }}" 
                                 alt="{{ $product->p_name }}" 
                                 style="width:100%; height:auto; display:block; max-height:400px; object-fit:cover;">
                        </td>
                    </tr>
                    @endif

                    <!-- التفاصيل -->
                    <tr>
                        <td style="padding: 24px; text-align:center;">
                            <span style="background-color:#e0f2fe; color:#0369a1; font-size:12px; font-weight:bold; padding:4px 12px; border-radius:20px; display:inline-block; margin-bottom:12px;">
                                ✨ وصل حديثًا
                            </span>
                            <h2 style="color:#111827; margin:0 0 10px 0; font-size:20px;">{{ $product->p_name }}</h2>
                            <p style="color:#4b5563; font-size:14px; line-height:1.6; margin:0 0 16px 0;">
                                {{ $product->p_description }}
                            </p>
                            
                            <div style="font-size:22px; color:#18C58F; font-weight:bold; margin-bottom:20px;">
                                {{ number_format((float) $product->p_price, 2) }} ر.ي
                            </div>

                            <a href="https://nice-store.alwaysdata.net" 
                               style="background-color:#18C58F; color:#ffffff; text-decoration:none; padding:12px 32px; border-radius:8px; font-weight:bold; display:inline-block; font-size:15px;">
                               تسوق الآن 🛒
                            </a>
                        </td>
                    </tr>

                    <!-- الفوتر -->
                    <tr>
                        <td style="background-color:#f9fafb; padding:16px; text-align:center; border-top:1px solid #f3f4f6;">
                            <p style="color:#9ca3af; font-size:12px; margin:0;">
                                وصلتك هذه الرسالة لأنك مشترك في متجر NICE.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>