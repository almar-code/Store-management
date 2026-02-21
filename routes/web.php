<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
Route::get('/', function () {
    return view('welcome');
});
Route::get('addproduct', [ProductController::class, 'AddProduct']);

Route::get('addsection', [ProductController::class, 'AddSection']);

Route::get('products', [ProductController::class, 'Products']);

