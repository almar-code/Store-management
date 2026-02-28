
@extends('layouts.master')

@section('link')
    <link rel="stylesheet" href="{{ asset('assets/css/login.css') }}">
@endsection

@section('content')
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

                    <form id="loginForm">
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

