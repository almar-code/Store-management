<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DiscountController extends Controller
{
    /**
     * Handle the incoming request.
     */
   public function AddDiscount(){
        return view('Discount.AddDiscount', []);
    }
}
