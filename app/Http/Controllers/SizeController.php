<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SizeController extends Controller
{
    public function AddSize(){
        return view('Sizes.addsize', []);
    }
}
