<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function AddProduct(){
        return view('Products.addproduct', []);
    }
    public function Products()
    {
          return view('Products.Products', []);
    }
}
