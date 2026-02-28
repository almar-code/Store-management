<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ColorsController extends Controller
{
    /**
     * Handle the incoming request.
     */
   public function AddColor(){
        return view('Colors.addColor', []);
    }
    public function Colors(){
        return view('Colors.Colors', []);
    }
}
