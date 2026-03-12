@extends('layouts.master')
@section('content')

    <!-- Contact Section -->
    <section id="contact" class="contact section">
        <!-- Section Title -->
        <div class="col-lg-8 offset-lg-2 text-center">
            <div class="section-title">
                <h3>
                    @if (!isset($editsize))
                        Add 
                    @endif
                    @if (isset($editsize))
                        update 
                    @endif
                    
                <span class="orange-text">size</span></h3>
            </div>
        </div>
        <div class="container my-5" style="direction: rtl; text-align: right">
          <div class="row justify-content-center">
            
            <div class="col-14 col-md-10 col-lg-8">
                

                <div class="form-container">
                    <div class="d-flex flex-md-row justify-content-between align-items-center mb-3">
                    @if (!isset($editsize))
                        <h3>نموذج إضافة مقاس المنتج </h3>
                    @endif
                    @if (isset($editsize))
                        <h3>نموذج تعديل مقاس المنتج </h3>
                    @endif
                       <a href="{{ url()->previous() }}">
                            <i class="bi bi-arrow-left fs-4 text-dark"></i>
                        </a>
                    </div>

                    <form  action="{{ isset($editsize) ? '/update-size/' . $editsize->size_id : '/add-size/' .$productID }}" method="post"
                        id="dataForm">
                        @csrf
                        
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="productName" name="productName"  placeholder="Full Name" required=""
                            @if (!isset($editsize))
                            value="{{ $productName ?? 'uu' }}"
                            @endif
                            @if (isset($editsize))
                            value="  {{ isset($editsize) ? $editsize->product->p_name : ($productName ?? 'uu') }}"
                            @endif
                            readonly>
                            <input type="hidden" name="product_id" value="{{ $editsize->product->p_id ?? '' }}">
                            <label for="productName">المنتج   </label>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="size_name" name="size_name" placeholder="Full Name" 
                            @if (isset($editsize))
                            value="{{ $editsize->size_name ?? 'uu' }}"
                            @endif
                            required="">
                            <label for="size_name">المقاس  </label>
                        </div>
                        @error('size_name')
                            <div class="form-error">
                                <i class="bi bi-exclamation-circle"></i>
                                الرجاء ادخال  مقاس المنتج 
                            </div>
                        @enderror
                        
                       
                        

                        <div class="d-grid" style="direction: ltr">
                            @if (!isset($editsize))
                                    <button type="submit" class="btn-submit" id="seaveBtn">

                                        إضافة <i class="bi bi-plus ms-2"></i>

                                    </button>
                                @endif
                                @if (isset($editsize))
                                    <button type="submit" class="btn-submit" id="seaveBtn">

                                        تعديل <i class="bi bi-pencil-square" style=" font-size:13px; margin: 3px"></i>

                                    </button>
                                @endif
                        </div>
                    </form>
                </div>

        </div> </div> </div>
    </section><!-- /Contact Section -->
@endsection

