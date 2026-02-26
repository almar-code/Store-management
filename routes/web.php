<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\loginController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\SizeController;
Route::get('/', function () {
    return view('welcome');
});
Route::get('addproduct', [ProductController::class, 'AddProduct']);

Route::get('addsection', [SectionController::class, 'AddSection']);
Route::get('sections', [SectionController::class, 'Sections']);
Route::get('addcategorie', [CategorieController::class, 'AddCategorie']);
Route::get('categorieManagement', [CategorieController::class, 'CategorieManagement']);

Route::get('products', [ProductController::class, 'Products']);
Route::get('login', [loginController::class, 'Login']);
Route::get('orders', [OrdersController::class, 'Orders']);
Route::get('orderDetails', [OrdersController::class, 'OrderDetails']);
Route::get('addsize', [SizeController::class, 'Addsize']);

