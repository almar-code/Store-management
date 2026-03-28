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
                @endif
                @if (isset($edituser))
                    update 
                @endif
                
                    <span class="orange-text">User</span></h3>
            </div>
        </div>
       
        <div class="container my-5" style="direction: rtl; text-align: right">
          <div class="row justify-content-center">
            
            <div class="col-14 col-md-10 col-lg-8">
                
                <div class="form-container">
                    <div class="d-flex flex-md-row justify-content-between align-items-center mb-3">

                    @if (!isset($edituser))
                        <h3>نموذج إضافة مستخدم</h3>
                    @endif
                    @if (isset($edituser))
                        <h3>نموذج تعديل المستخدم</h3>
                    @endif
                    <a href="{{ url()->previous() }}">
                            <i class="bi bi-arrow-left fs-4 text-dark"></i>
                    </a>
                    </div>
                
                    <form id="dataForm" action="{{ isset($edituser) ? '/update-user/' . $edituser->user_id : '/add-user' }}" method="post">
                        @csrf
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="userFullName" name="userFullName" placeholder="Full Name" required=""
                            @if (isset($edituser))
                            value="{{ $edituser->full_name }}"
                                
                            @endif>
                            <label for="userFullName">الاسم</label>
                            @error('userFullName')
                                    <div class="form-error">
                                        <i class="bi bi-exclamation-circle"></i>
                                        الرجاء اختيار اسم المستخدم   
                                    </div>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="userName" name="userName" placeholder="Full Name" required=""
                            @if (isset($edituser))
                            value="{{ $edituser->username }}"
                                
                            @endif>
                            
                            <label for="userName">اسم المستخدم</label>
                            @error('userName')
                                    <div class="form-error">
                                        <i class="bi bi-exclamation-circle"></i>
                                        الرجاء اختيار الاسم  
                                    </div>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="password_hash" name="password_hash" placeholder="كلمة السر">
                            <label for="password_hash">كلمة السر</label>

                            @if(isset($edituser))
                                <small class="text-muted">اترك الحقل فارغ إذا لا تريد تغيير كلمة السر</small>
                            @endif

                            @error('password_hash')
                                <div class="form-error">
                                    <i class="bi bi-exclamation-circle"></i>
                                    الرجاء اختيار كلمة السر
                                </div>
                            @enderror
                        </div>

                        
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" id="email" name="email" placeholder="Subject" required=""
                            @if (isset($edituser))
                            value="{{ $edituser->email }}"
                                
                            @endif>
                            
                            <label for="email">الاميل </label>
                            @error('email')
                                    <div class="form-error">
                                        <i class="bi bi-exclamation-circle"></i>
                                        الرجاء اختيار الاميل   
                                    </div>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="userAddress" name="userAddress" placeholder="Subject" required=""
                            @if (isset($edituser))
                            value="{{ $edituser->address }}"
                                
                            @endif>
                            
                            <label for="userAddress">عنوان المستخدم</label>
                            @error('subcat_image')
                                    <div class="form-error">
                                        <i class="bi bi-exclamation-circle"></i>
                                        الرجاء اختيار عنوان المستخدم  
                                    </div>
                            @enderror
                        </div>
                        <div class="d-grid" style="direction: ltr">
                            @if (!isset($edituser))
                                    <button type="button" class="btn-submit" id="saveBtn" onclick="activateLoading('dataForm', 'saveBtn');">
                                        إضافة <i class="bi bi-plus ms-2"></i>
                                    </button>
                            @endif

                            @if (isset($edituser))
                                    <button type="button" class="btn-submit" id="saveBtn" onclick="activateLoading('dataForm', 'saveBtn');">
                                        تعديل <i class="bi bi-pencil-square" style=" font-size:13px; margin: 3px"></i>
                                    </button>
                            @endif
                        </div>
                    </form>
                </div>

        </div> </div> </div>
    </section><!-- /Contact Section -->
@endsection

