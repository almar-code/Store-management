@extends('layouts.master')
@section('content')

    <!-- Contact Section -->
    <section id="contact" class="contact section">
        <!-- Section Title -->
        <div class="col-lg-8 offset-lg-2 text-center">
            <div class="section-title">
                <h3>Add <span class="orange-text">User</span></h3>
            </div>
        </div>
       
        <div class="container my-5" style="direction: rtl; text-align: right">
          <div class="row justify-content-center">
            
            <div class="col-14 col-md-10 col-lg-8">
                
                <div class="form-container">
                     <h3>نموذج إضافة مستخدم</h3>
                    <form action="forms/contact.php" method="post">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="userFullName" name="userFullName" placeholder="Full Name" required="">
                            <label for="userFullName">اسم المستخدم</label>
                        </div>
                        
                        <div class="form-floating mb-3">
                            <input type="tel" class="form-control" id="usrePhone" name="usrePhone" placeholder="Subject" required="">
                            <label for="usrePhone">رقم الهاتف</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="userAddress" name="userAddress" placeholder="Subject" required="">
                            <label for="userAddress">عنوان المستخدم</label>
                        </div>
                        <div class="d-grid" style="direction: ltr">
                            <button type="submit" class="btn-submit">إضافة <i class="bi bi-plus ms-2"></i></button>
                        </div>
                    </form>
                </div>

        </div> </div> </div>
    </section><!-- /Contact Section -->
@endsection

