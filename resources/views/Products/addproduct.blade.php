@extends('layouts.master')
@section('content')

    <!-- Contact Section -->
    <section id="contact" class="contact section">
        <!-- Section Title -->
        <div class="col-lg-8 offset-lg-2 text-center">
            <div class="section-title">
                <h3>Add <span class="orange-text">Product</span></h3>
            </div>
        </div>
        {{-- <div class="container" style="direction: rtl;text-align: right">
            <div class="form-container">
                <h3>نموذج إضافة منتج</h3>
                <p>قم بإدخال بيانات المنتج من خلال هذا النموذج
                </p>
                <form action="forms/contact.php" method="post" class="">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="productName" name="productName" placeholder="Full Name"
                            required="">
                        <label for="productName">اسم المنتج</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="file" class="form-control" id="productImage" name="productImage" placeholder="Subject"
                            required="">
                        <label for="productImage">صورة المنتج</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="number" class="form-control" id="productPrice" name="m"
                            placeholder="Subject" required="">
                        <label for="productPrice">سعر المنتج</label>
                    </div>
                    <div class="form-floating mb-3">
                        <textarea class="form-control" id="productDescription" name="productDescription" rows="3"
                            placeholder="productColor" style="height: 150px;" required=""></textarea>
                        <label for="productDescription">وصف المنتج</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="color" class="form-control" id="productColor" name="productColor"
                            placeholder="productColor" required="">
                        <label for="productColor">لون المنتج</label>
                    </div>
                    <div class="form-floating mb-3">
                        <label for="productSubcategory">فئة المنتج</label>
                        <select class="form-control" id="productSubcategory" name="productSubcategory"
                            placeholder="productSubcategory" required="">
                            <option>رسمي </option>
                            <option>تركي </option>
                            <option>تخرج </option>
                        </select>
                    </div>


                   

                    <div class="d-grid" style="direction: ltr">
                        <button type="submit" class="btn-submit">إضافة <i class="bi bi-plus ms-2"></i></button>
                    </div>
                </form>
            </div>
        </div> --}}



        <div class="container my-5" style="direction: rtl; text-align: right">
          <div class="row justify-content-center">
            
            <div class="col-14 col-md-10 col-lg-8">
                
                <div class="form-container">
                    {{-- <h3>نموذج إضافة منتج</h3>
                    <p>قم بإدخال بيانات المنتج من خلال هذا النموذج</p> --}}
                    
                    <form action="forms/contact.php" method="post">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="productName" name="productName" placeholder="Full Name" required="">
                            <label for="productName">اسم المنتج</label>
                        </div>
                        
                        <div class="form-floating mb-3">
                            <input type="file" class="form-control" id="productImage" name="productImage" placeholder="Subject" required="">
                            <label for="productImage">صورة المنتج</label>
                        </div>
                        
                        <div class="form-floating mb-3">
                            <input type="number" class="form-control" id="productPrice" name="m" placeholder="Subject" required="">
                            <label for="productPrice">سعر المنتج</label>
                        </div>
                        
                        <div class="form-floating mb-3">
                            <textarea class="form-control" id="productDescription" name="productDescription" rows="3" placeholder="productColor" style="height: 150px;" required=""></textarea>
                            <label for="productDescription">وصف المنتج</label>
                        </div>
                        
                        <div class="form-floating mb-3">
                            <input type="color" class="form-control" id="productColor" name="productColor" placeholder="productColor" required="">
                            <label for="productColor">لون المنتج</label>
                        </div>
                        
                        <div class="form-floating mb-3">
                            <select class="form-control" id="productSubcategory" name="productSubcategory" required="">
                                <option value="" selected disabled>اختر الفئة...</option>
                                <option>رسمي </option>
                                <option>تركي </option>
                                <option>تخرج </option>
                            </select>
                            <label for="productSubcategory">فئة المنتج</label>
                        </div>

                        <div class="d-grid" style="direction: ltr">
                            <button type="submit" class="btn-submit">إضافة <i class="bi bi-plus ms-2"></i></button>
                        </div>
                    </form>
                </div>

        </div> </div> </div>
    </section><!-- /Contact Section -->

@endsection

