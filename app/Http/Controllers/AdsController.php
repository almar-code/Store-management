<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class adsController extends Controller
{
   public function AddAds(){
        return view('Ads.addads', []);
    }
}
