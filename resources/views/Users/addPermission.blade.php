@extends('layouts.master')
@section('link')
    <style>
        .radio-buttons-container {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 24px;
            padding: 20px 0;
        }

        .radio-button {
            display: inline-block;
            position: relative;
            cursor: pointer;
        }

        .radio-button__input {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .radio-button__label {
            display: inline-block;
            padding-left: 30px;
            margin-bottom: 10px;
            position: relative;
            font-size: 16px;
            color: #000000;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.23, 1, 0.320, 1);
        }

        .radio-button__custom {
            position: absolute;
            top: 50%;
            left: 0;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 2px solid #000000;
            transition: all 0.3s cubic-bezier(0.23, 1, 0.320, 1);
        }

        .radio-button__input:checked+.radio-button__label .radio-button__custom {
            transform: translateY(-50%) scale(0.9);
            border: 5px solid #008870;
            color: #008870;
        }

        .radio-button__input:checked+.radio-button__label {
            color: #008870;
        }

        .radio-button__label:hover .radio-button__custom {
            transform: translateY(-50%) scale(1.2);
            border-color: #008870;
            box-shadow: 0 0 10px #36b8a0;
        }
    </style>
@endsection
@section('content')
    <!-- Contact Section -->
    <section id="contact" class="contact section">
        <!-- Section Title -->
        <div class="col-lg-8 offset-lg-2 text-center">
            <div class="section-title">
                <h3>Add <span class="orange-text">Permission</span></h3>
            </div>
        </div>

        <div class="container my-5" style="direction: rtl; text-align: right">
            <div class="row justify-content-center">

                <div class="col-14 col-md-10 col-lg-8">

                    <div class="form-container">
                        <div style="display: flex;justify-content: space-between">
                             <h3>نموذج إضافة صلاحية للمستخدم</h3>
                            <a href="javascript:history.back()">
                                <i class="bi bi-arrow-left fs-4 text-dark"></i>
                            </a>
                        </div>
                       
                        <form action="forms/contact.php" method="post">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="userFullName" name="userFullName"
                                    placeholder="Full Name" required="" value="حمد عبدو" readonly>
                                <label for="userFullName">الاسم</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="userFullName" name="userFullName"
                                    placeholder="Full Name" required="">
                                <label for="userFullName">اسم المستخدم</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="tel" class="form-control" id="usrePhone" name="usrePhone"
                                    placeholder="Subject" required="">
                                <label for="usrePhone">كلمة المرور</label>
                            </div>
                            <div class="form-floating mb-3">
                                <select class="form-control" id="productSubcategory" name="productSubcategory"
                                    required="">
                                    <option value="" selected disabled>اختر الصلاحية...</option>
                                    <option>مدير الطلبات </option>
                                    <option>مدير المخزون </option>
                                </select>
                                <label for="productSubcategory">الصلاحيات</label>
                            </div>
                            <div class="radio-buttons-container">
                                <div class="radio-button">
                                    <input name="radio-group" id="radio2" class="radio-button__input" type="radio">
                                    <label for="radio2" class="radio-button__label">
                                        <span class="radio-button__custom"></span>
                                        نشيط
                                    </label>
                                </div>
                                <div class="radio-button">
                                    <input name="radio-group" id="radio1" class="radio-button__input" type="radio">
                                    <label for="radio1" class="radio-button__label">
                                        <span class="radio-button__custom"></span>

                                        غير نشيط
                                    </label>
                                </div>
                            </div>
                            <div class="d-grid" style="direction: ltr">
                                <button type="submit" class="btn-submit">إضافة <i class="bi bi-plus ms-2"></i></button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </section><!-- /Contact Section -->
@endsection
