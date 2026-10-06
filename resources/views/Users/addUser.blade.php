@extends('Layouts.master')
@section('content')
    <!-- Contact Section -->
    <section id="contact" class="contact section">
        <!-- Section Title -->
        <div class="col-lg-8 offset-lg-2 text-center">
            <div class="section-title">
                <h3>
                    @if (!isset($edituser))
                        Add
                    @else
                        update
                    @endif

                    <span class="orange-text">User</span>
                </h3>
            </div>
        </div>

        <div class="container my-5" style="direction: rtl; text-align: right">
            <div class="row justify-content-center">

                <div class="col-14 col-md-10 col-lg-8">

                    <div class="form-container">
                        <div class="d-flex flex-md-row justify-content-between align-items-center mb-3">

                            @if (!isset($edituser))
                                <h3>نموذج إضافة مستخدم</h3>
                            @else
                                <h3>نموذج تعديل المستخدم</h3>
                            @endif
                            <a href="{{ url()->previous() }}">
                                <i class="bi bi-arrow-left fs-4 text-dark"></i>
                            </a>
                        </div>

                        <form id="dataForm"
                            action="{{ isset($edituser) ? '/update-user/' . $edituser->user_id : '/add-user' }}"
                            method="post">
                            @csrf

                            {{-- الاسم الكامل --}}
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="userFullName" name="userFullName"
                                    placeholder="Full Name" required
                                    value="{{ old('userFullName', $edituser->full_name ?? '') }}">
                                <label for="userFullName">الاسم</label>
                                @error('userFullName')
                                    <div class="form-error">
                                        <i class="bi bi-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- اسم المستخدم --}}
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="userName" name="userName"
                                    placeholder="Full Name" required
                                    pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[a-zA-Z0-9_]{6,}"
                                    title="يجب أن يتكون اسم المستخدم من 6 خانات على الأقل ويحتوي على أحرف صغيرة وكبيرة وأرقام"
                                    value="{{ old('userName', $edituser->username ?? '') }}">

                                <label for="userName">اسم المستخدم</label>

                                @error('userName')
                                    <div class="form-error">
                                        <i class="bi bi-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- كلمة السر --}}
                            <div class="form-floating mb-3">
                                <input type="password" class="form-control" id="password_hash" name="password_hash"
                                    placeholder="كلمة السر" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_]).{8,}"
                                    title="يجب أن تحتوي كلمة السر على 8 أرقام/أحرف على الأقل، وتتضمن حرفاً كبيراً، وحرفاً صغيراً، وارقام ورمزاً خاصاً"
                                    {{ !isset($edituser) ? 'required' : '' }} 
                                     value="{{ old('password_hash') }}">
                                <label for="password_hash">كلمة السر</label>

                                @if (isset($edituser))
                                    <small class="badge bg-danger">اترك الحقل فارغ إذا لا تريد تغيير كلمة السر</small>
                                @endif

                                @error('password_hash')
                                    <div class="form-error">
                                        <i class="bi bi-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- البريد الإلكتروني --}}
                            <div class="form-floating mb-3">
                                <input type="email" class="form-control" id="email" name="email"
                                    placeholder="Subject" required
                                    value="{{ old('email', $edituser->email ?? '') }}">

                                <label for="email">الإيميل</label>
                                @error('email')
                                    <div class="form-error">
                                        <i class="bi bi-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- رقم الهاتف --}}
                            <div class="form-floating mb-3">
                                <input type="tel" class="form-control" id="phone" name="phone" placeholder="Phone"
                                    required pattern="^\+?[0-9]+$"
                                    title="الرجاء إدخال أرقام فقط، ويوضع رمز (+) في البداية فقط إذا لزم الأمر"
                                    oninput="this.value = this.value.replace(/(?!^\+)[^\d]/g, '');"
                                    value="{{ old('phone', $edituser->phone ?? '') }}">

                                <label for="phone">رقم الهاتف</label>

                                @error('phone')
                                    <div class="form-error">
                                        <i class="bi bi-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- عنوان المستخدم --}}
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="userAddress" name="userAddress"
                                    placeholder="Subject" required
                                    value="{{ old('userAddress', $edituser->address ?? '') }}">

                                <label for="userAddress">عنوان المستخدم</label>
                                @error('userAddress')
                                    <div class="form-error">
                                        <i class="bi bi-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="d-grid" style="direction: ltr">
                                @if (!isset($edituser))
                                    <button type="button" class="btn-submit" id="saveBtn"
                                        onclick="activateLoading('dataForm', 'saveBtn');">
                                        إضافة <i class="bi bi-plus ms-2"></i>
                                    </button>
                                @else
                                    <button type="button" class="btn-submit" id="saveBtn"
                                        onclick="activateLoading('dataForm', 'saveBtn');">
                                        تعديل <i class="bi bi-pencil-square" style="font-size:13px; margin: 3px"></i>
                                    </button>
                                @endif
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </section><!-- /Contact Section -->
@endsection