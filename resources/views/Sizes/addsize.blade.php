@extends('layouts.master')
@section('content')

    <!-- Contact Section -->
    <section id="contact" class="contact section">
        <!-- Section Title -->
        <div class="col-lg-8 offset-lg-2 text-center">
            <div class="section-title">
                <h3>Add <span class="orange-text">size</span></h3>
            </div>
        </div>
       
        <div class="container my-5" style="direction: rtl; text-align: right">
          <div class="row justify-content-center">
            
            <div class="col-14 col-md-10 col-lg-8">
                
                <div class="form-container">
                    <div class="d-flex flex-md-row justify-content-between align-items-center mb-3">
                            <h3 class="mb-2 mb-md-0 fs-5">نموذج إضافة المقاسات</h3>
                       <a href="/products"> <i class="fas fa-arrow-left"></i></a>
                    </div>

                    <form action="forms/contact.php" method="post">
                        
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="productPrice" name="productPrice"  placeholder="Full Name" required=""
                            value="حمد عبدو" readonly>
                            <label for="productName">المنتج   </label>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="SizeName" name="SizeName" placeholder="Full Name" required="">
                            <label for="SizeName">المقاس  </label>
                        </div>
                        
                       
                        

                        <div class="d-grid" style="direction: ltr">
                            <button type="submit" class="btn-submit">إضافة <i class="bi bi-plus ms-2"></i></button>
                        </div>
                    </form>
                </div>

        </div> </div> </div>
    </section><!-- /Contact Section -->
@endsection

