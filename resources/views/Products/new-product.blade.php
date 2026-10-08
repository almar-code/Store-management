
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>منتج جديد - NICE</title>
</head>
<body style="margin:0;background:#f4f7f6;font-family:Arial,sans-serif;direction:rtl;">
    <div style="max-width:600px;margin:30px auto;background:#fff;border-radius:12px;overflow:hidden;">
        <div style="background:#18C58F;padding:24px;text-align:center;color:white;">
            <h1>NICE Smart Store</h1>
            <p>وصل حديثًا إلى متجرنا</p>
        </div>

        <div style="padding:24px;text-align:center;">
            <h2>{{ $product->p_name }}</h2>

            @if($product->p_image)
                <img
                    src="{{ asset('storage/uploads/products/' . $product->p_image) }}"
                    alt="{{ $product->p_name }}"
                    style="width:100%;max-width:350px;border-radius:10px;"
                >
            @endif

            <p>{{ $product->p_description }}</p>

            <h2 style="color:#119b70;">
                {{ number_format((float) $product->p_price, 2) }}
            </h2>

            <p>تعرّف على المنتج الجديد في متجر NICE.</p>
        </div>
    </div>
</body>
</html>