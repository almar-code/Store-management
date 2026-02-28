@extends('layouts.master')
@section('content')

    
    <section id="contact" class="contact section">
        
        <div class="col-lg-8 offset-lg-2 text-center">
            <div class="section-title">
                <h3>Add <span class="orange-text">Advertisement</span></h3>
            </div>
        </div>
       
        <div class="container my-5" style="direction: rtl; text-align: right">
          <div class="row justify-content-center">
            
            <div class="col-14 col-md-10 col-lg-8">
                
                <div class="form-container">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                            <h3>نموذج إضافة اعلانات </h3>
                       <a href="/"> <i class="fas fa-arrow-left"></i></a>
                    </div>

                    <form action="forms/contact.php" method="post">
                        
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="AdsTitle" name="AdsTitle"  placeholder="Advertisement Title" required="">
                            <label for="AdsTitle">اسم الاعلان    </label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="file" class="form-control" id="AdsImage" name="AdsImage" placeholder="Subject" required="">
                            <label for="AdsImage">صورة المنتج</label>
                        </div>

                       <div class="form-floating mb-3">
                            <input type="date" class="form-control" id="StartDate" name="StartDate" placeholder="اختر التاريخ" required>
                            <label for="StartDate">تاريخ البدايه</label>
                        </div>
                        
                       <div class="form-floating mb-3">
                            <input type="date" class="form-control" id="EndDate" name="EndDate" placeholder="اختر التاريخ" required>
                            <label for="EndDate">تاريخ النهايه </label>
                        </div>

                        <div class="d-grid" style="direction: ltr">
                            <button type="submit" class="btn-submit">إضافة <i class="bi bi-plus ms-2"></i></button>
                        </div>
                    </form>
                </div>

        </div> </div> </div>
    </section>
@endsection

