<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function AddSection(){
        return view('Sections.addsection', []);
    }
}
