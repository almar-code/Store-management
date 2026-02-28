@extends('layouts.master')
@section('content')

    <!-- Contact Section -->
    <section id="contact" class="contact section">
        <!-- Section Title -->
        <div class="col-lg-8 offset-lg-2 text-center">
            <div class="section-title">
                <h3>Add <span class="orange-text">Color</span></h3>
            </div>
        </div>
       
        <div class="container my-5" style="direction: rtl; text-align: right">
          <div class="row justify-content-center">
            
            <div class="col-14 col-md-10 col-lg-8">
                
                <div class="form-container">
                     <h3>نموذج إضافة لون للمنتج</h3>
                    <form action="forms/contact.php" method="post">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" readonly required="">
                            <label for="productName">اسم المنتج</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="color" class="form-control" id="productColor" name="productColor" placeholder="productColor" required="">
                            <label for="productColor">لون المنتج</label>
                        </div>
                         <div class="form-floating mb-3">
                            <input type="number" class="form-control" id="ColorName" name="ColorName" placeholder="Subject" required="">
                            <label for="ColorName">اسم اللون</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="file" class="form-control" id="productImages" name="productImages" placeholder="Subject" required="">
                            <label for="productImages">صور المنتج</label>
                        </div>
                        
                        <div class="d-grid" style="direction: ltr">
                            <button type="submit" class="btn-submit">إضافة <i class="bi bi-plus ms-2"></i></button>
                        </div>
                    </form>
                </div>

        </div> </div> </div>
    </section><!-- /Contact Section -->

@endsection

