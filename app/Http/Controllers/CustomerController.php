<?php

namespace App\Http\Controllers;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Handle the incoming request.
     */
   
        public function Customers()
{
    try {

        $customers = Customer::all();
        return view('Customers.customers', compact('customers'));

    } catch (\Exception $e) {
        return redirect()->back()->with('error','حدث خطأ أثناء جلب العملاء');

    }
}
    }

