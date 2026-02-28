
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

                        <button class="btn-login">LOGIN</button>
                    </form>
                   <a href="/"> <button  class="exit-btn" onclick="handleExit()">
                   <i class="fas fa-arrow-left"></i> Exit
                </button></a>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

