<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="15">
    <title>المتجر قيد التحديث</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            font-family: "Segoe UI", Tahoma, Arial, sans-serif;
            background: linear-gradient(160deg, #f8fbfa 0%, #e6f5f2 100%);
            color: #212529;
        }

        .card {
            width: 100%;
            max-width: 460px;
            background: #fff;
            border: 1px solid #e3f0ed;
            border-radius: 22px;
            padding: 38px 28px 30px;
            text-align: center;
            box-shadow: 0 18px 45px rgba(0, 136, 112, 0.1);
        }

        /* أيقونة الدوران */
        .loader {
            position: relative;
            width: 110px;
            height: 110px;
            margin: 0 auto 22px;
        }

        .loader .ring {
            position: absolute;
            inset: 0;
            border-radius: 50%;
            border: 4px solid #e3f0ed;
            border-top-color: #008870;
            animation: spin 1.1s linear infinite;
        }

        .loader .core {
            position: absolute;
            inset: 16px;
            border-radius: 50%;
            background: linear-gradient(145deg, #00a386, #008870);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 25px rgba(0, 136, 112, 0.3);
        }

        .loader svg { width: 36px; height: 36px; fill: #fff; }

        @keyframes spin { to { transform: rotate(360deg); } }

        h1 { font-size: 22px; font-weight: 800; margin-bottom: 10px; color: #008870; }

        p { font-size: 15px; line-height: 1.9; color: #6c757d; margin-bottom: 22px; }

        .note {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: #f8fbfa;
            border: 1px dashed #cfe6e1;
            border-radius: 12px;
            padding: 11px 14px;
            font-size: 13.5px;
            color: #495057;
        }

        .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #e8a33d;
            animation: blink 1.2s ease-in-out infinite;
        }

        @keyframes blink { 50% { opacity: 0.25; } }

        @media (max-width: 480px) {
            .card { padding: 30px 18px 24px; border-radius: 18px; }
            h1 { font-size: 19px; }
            p { font-size: 14px; }
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="loader">
            <span class="ring"></span>
            <div class="core">
                <svg viewBox="0 0 24 24"><path d="M12 2 4 5v6c0 5 3.4 9.7 8 11 4.6-1.3 8-6 8-11V5l-8-3zm0 4a3 3 0 0 1 3 3v1h1v6H8v-6h1V9a3 3 0 0 1 3-3zm0 2a1 1 0 0 0-1 1v1h2V9a1 1 0 0 0-1-1z"/></svg>
            </div>
        </div>

        <h1>المتجر قيد التحديث</h1>
        <p>نعمل الآن على تحديث بيانات المتجر بأمان.<br>سيعود كل شيء خلال دقائق قليلة.</p>

        <div class="note">
            <span class="dot"></span>
            ستتحدث الصفحة تلقائياً، لا حاجة لإعادة التحميل
        </div>
    </div>
</body>
</html>