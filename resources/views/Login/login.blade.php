<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Nice</title>
    <meta name="description" content="">
    <meta name="keywords" content="">

    <!-- Favicons -->
    <link href="assets/img/ligth-mode-logo.gif" rel="icon">
    <link href="assets/img/ligth-mode-logo.gif" rel="apple-touch-icon">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

    <!-- Main CSS File -->
    <link href="assets/css/main.css" rel="stylesheet">
    <link href="assets/css/card.css" rel="stylesheet">
    <link href="assets/css/login.css" rel="stylesheet">    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>

<body class="index-page">
    <div class="login-page">
    <div class="login-container">

        <!-- الخلفية -->
        <div class="visual-side"></div>

        <div class="overlay">
            <div class="empty-side"></div>
            <div class="form-side">
                <div class="form-box">

                    <div class="avatar-circle">
                        <img src="assets/img/photos/images.png">
                    </div>

                    <h2>WELCOME</h2>

                    <form id="loginForm" >
                        <div class="input-group">
                            <div class="input-wrapper">
                                <i class="fas fa-user"></i>
                                <input type="text" required>
                                <label for="username">Username</label>
                            </div>
                        </div>

                        
                        <div class="input-group">
                            <div class="input-wrapper">
                                <i class="fas fa-lock"></i>
                                <input type="password"  required>
                                <label for="Password">Password</label>
                            </div>
                        </div>

                        <button class="btn-login" id="loginBtn">LOGIN</button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
<script>
        // التعامل مع نموذج الدخول
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('loginBtn');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            btn.disabled = true;
            
            setTimeout(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Welcome!',
                    text: 'Logged in successfully',
                    showConfirmButton: false,
                    timer: 1500
                });
                btn.innerHTML = 'LOGIN';
                btn.disabled = false;
            }, 1000);
        });

        
    </script>
@endsection


