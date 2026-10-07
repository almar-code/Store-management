<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * عرض قائمة العملاء
     */
    public function Customers()
    {
        try {
            $customers = Customer::all();
            return view('Customers.customers', compact('customers'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء جلب العملاء');
        }
    }
}