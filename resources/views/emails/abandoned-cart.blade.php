<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سلتك بانتظارك</title>
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
                    <td style="padding:24px 24px 8px; text-align:center;">
                        <span style="background-color:#e6faf3; color:#00B387; font-size:12px; font-weight:bold; padding:4px 12px; border-radius:20px; display:inline-block; margin-bottom:12px;">
                            سلتك بانتظارك
                        </span>
                        <h2 style="margin:0 0 8px; color:#111827; font-size:20px;">
                            مرحبا {{ $customer->name ?? 'عميلنا العزيز' }} 💚
                        </h2>
                        <p style="margin:0; color:#6b7280; font-size:14px; line-height:1.7;">
                            لاحظنا أنك تركت بعض المنتجات في سلتك، ما زالت محفوظة لك.هل ترغب في إتمام الشراء؟.
                        </p>
                    </td>
                </tr>

                {{-- المنتجات --}}
                <tr>
                    <td style="padding:16px 24px;">
                        @foreach($items as $item)
                            <table role="presentation" width="100%" style="border:1px solid #eef0f3; border-radius:12px; margin-bottom:10px;">
                                <tr>
                                    <td width="70" style="padding:8px;">
                                        @if(!empty($item->product?->p_image))
                                            <img src="{{ asset('storage/uploads/products/' . $item->product->p_image) }}"
                                                 width="60" height="60" style="border-radius:10px; object-fit:cover; display:block;">
                                        @endif
                                    </td>
                                    <td style="padding:8px; color:#111827; font-size:14px;">
                                        <strong>{{ $item->product?->p_name }}</strong><br>
                                        <span style="color:#6b7280; font-size:12px;">الكمية: {{ $item->quantity }}</span>
                                    </td>
                                    <td style="padding:8px; color:#00B387; font-weight:bold; font-size:14px; white-space:nowrap;">
                                        {{ $item->product?->p_price }}
                                    </td>
                                </tr>
                            </table>
                        @endforeach
                    </td>
                </tr>

                {{-- الزر --}}
                <tr>
                    <td style="padding:8px 24px 28px; text-align:center;">
                        <a href="{{ $appUrl }}"
                           style="background-color:#00B387; color:#ffffff; text-decoration:none; padding:12px 32px; border-radius:12px; font-weight:bold; font-size:15px; display:inline-block;">
                            أكمل طلبك الآن
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
