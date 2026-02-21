<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CategorieController extends Controller
{
    public function AddCategorie(){
        return view('Categories.addcategorie', []);
    }


    
    public function CategorieManagement(){
        

        return view('Categories.categorieManagement', []);
    }
}
