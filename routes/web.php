<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\ColorsController;
use App\Http\Controllers\AdsController;
Route::get('/', function () {
    return view('welcome');
});
Route::get('addproduct', [ProductController::class, 'AddProduct']);

Route::get('addsection', [SectionController::class, 'AddSection']);
Route::get('sections', [SectionController::class, 'Sections']);
Route::get('addcategorie', [CategorieController::class, 'AddCategorie']);
Route::get('categorieManagement', [CategorieController::class, 'CategorieManagement']);

Route::get('products', [ProductController::class, 'Products']);
Route::get('login', [LoginController::class, 'Login']);
Route::get('orders', [OrdersController::class, 'Orders']);
Route::get('orderDetails', [OrdersController::class, 'OrderDetails']);
Route::get('addUser', [UserController::class, 'AddUser']);
Route::get('users', [UserController::class, 'Users']);
Route::get('addPermission', [UserController::class, 'AddPermission']);
Route::get('permission', [UserController::class, 'Permission']);
Route::get('addDiscount', [DiscountController::class, 'AddDiscount']);
Route::get('addsize', [SizeController::class, 'Addsize']);
Route::get('addColor', [ColorsController::class, 'AddColor']);
Route::get('colors', [ColorsController::class, 'Colors']);
Route::get('sizeManagement', [SizeController::class, 'SizeManagement']);
Route::get('addads', [AdsController::class, 'AddAds']);
