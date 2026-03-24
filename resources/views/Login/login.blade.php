<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Login - Nice</title>
    <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/login.css') }}" rel="stylesheet">    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body class="index-page">
    <div class="login-page">
        <div class="login-container">
            <div class="visual-side"></div>
            <div class="overlay">
                <div class="empty-side"></div>
                <div class="form-side">
                    <div class="form-box">
                        <div class="avatar-circle">
                            <img src="{{ asset('assets/img/photos/images.png') }}">
                        </div>
                        <h2>WELCOME</h2>

                        @if(session('error'))
                            <div class="alert alert-danger py-2 text-center" style="font-size: 14px; top: -20px; ">
                                {{ session('error') }}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-warning py-2 small">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form id="loginForm" action="{{ url('/login-user') }}" method="post">
                            @csrf
                            <div class="input-group">
                                <div class="input-wrapper">
                                    <i class="fas fa-user"></i>
                                    <input type="text" name="username" value="{{ old('username') }}" required>
                                    <label for="username">User name</label>
                                </div>
                            </div>

                            <div class="input-group">
                                <div class="input-wrapper">
                                    <i class="fas fa-lock"></i>
                                    <input type="password" name="password" required>
                                    <label for="Password">Password</label>
                                </div>
                            </div>

                            <button type="submit" class="btn-login" id="saveBtn">
                                LOGIN <i class="fas fa-sign-in-alt ms-2"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
    const loginForm = document.getElementById('loginForm'); // تأكد أن الـ form لديه id="loginForm"
    const saveBtn = document.getElementById('saveBtn');

    if (loginForm) {
        loginForm.addEventListener('submit', function() {
            // 2. تغيير محتوى الزر إلى دائرة الانتظار
            saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> جاري التحقق...';
            
            // 3. تعطيل الزر لمنع الضغط المتكرر
            saveBtn.disabled = true;
            saveBtn.style.cursor = 'not-allowed';
        });
    }
</script>
</body>

</html>