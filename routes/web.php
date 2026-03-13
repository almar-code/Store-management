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
use Illuminate\Http\Request;
Route::get('/', function () {
    return view('welcome');
});

Route::get('/addsection', [SectionController::class, 'AddSection']);// صفحة إضافة قسم
Route::get('/sections',[SectionController::class,'Sections']); // عرض الأقسام
Route::post('/add-section',[SectionController::class,'store']);// إضافة قسم
Route::get('/edit-section/{id}',[SectionController::class,'edit']);// تحميل نفس الصفحة الاضافة لكن مع بيانات القسم للتعديل
Route::post('/update-section/{id}',[SectionController::class,'update']);// تعديل القسم
Route::get('/delete-section/{id}',[SectionController::class,'destroy']);// حذف قسم


Route::get('/addcategorie', [CategorieController::class, 'AddCategorie']);
Route::get('/categorieManagement', [CategorieController::class, 'CategorieManagement']);
Route::post('/add-categorie', [CategorieController::class, 'store']);
Route::get('/edit-categorie/{id}', [CategorieController::class, 'edit']);
Route::post('/update-categorie/{id}',[CategorieController::class,'update']);
Route::get('/delete-categorie/{id}',[CategorieController::class,'destroy']);

Route::get('addproduct', [ProductController::class, 'AddProduct']);
Route::get('products', [ProductController::class, 'Products']);
Route::post('/add-product',[ProductController::class,'store']);// إضافة منتج
Route::get('/edit-product/{id}',[ProductController::class,'edit']);// تحميل نفس الصفحة الاضافة لكن مع بيانات المنتج للتعديل
Route::post('/update-product/{id}',[ProductController::class,'update']);// تعديل المنتج
Route::get('/delete-product/{id}',[ProductController::class,'destroy']);// حذف منتج

Route::get('login', [LoginController::class, 'Login']);
Route::get('orders', [OrdersController::class, 'Orders']);
Route::get('orderDetails', [OrdersController::class, 'OrderDetails']);
Route::get('addUser', [UserController::class, 'AddUser']);
Route::get('users', [UserController::class, 'Users']);
Route::get('addPermission', [UserController::class, 'AddPermission']);
Route::get('permission', [UserController::class, 'Permission']);

Route::get('addDiscount/{id}', [DiscountController::class, 'AddDiscount']);
Route::post('/add-discount/{id}', [DiscountController::class, 'store']);
Route::get('/delete-discount/{id}', [DiscountController::class, 'destroy']);


Route::get('addsize/{id}', [SizeController::class, 'Addsize']);
Route::get('sizeManagement/{id}', [SizeController::class, 'SizeManagement']);
Route::post('/add-size/{id}', [SizeController::class, 'store']);
Route::get('/edit-size/{id}', [SizeController::class, 'edit']);
Route::post('/update-size/{id}', [SizeController::class, 'update']);
Route::get('/delete-size/{id}', [SizeController::class, 'destroy']);


Route::get('addColor/{id}', [ColorsController::class, 'AddColor']);
Route::post('/add-color/{id}', [ColorsController::class, 'store']);
Route::post('/update-color/{id}', [ColorsController::class, 'update']);
Route::get('/edit-color/{id}', [ColorsController::class, 'edit']);
Route::get('colors/{id}', [ColorsController::class, 'Colors']);
Route::get('/delete-categorie/{id}',[CategorieController::class,'destroy']);
Route::get('/delete-color/{id}',[ColorsController::class,'destroy']);

Route::get('addads', [AdsController::class, 'AddAds']);
