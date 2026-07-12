<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SectionController;
use App\Http\Controllers\Api\CategorieController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\ColorsController;
use App\Http\Controllers\AdsController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\VideoInteractionController;
use App\Http\Controllers\Api\CartController;

use Illuminate\Http\Request;
Route::get('/sections', [SectionController::class, 'index']);
Route::get('/categories', [CategorieController::class, 'index']);
Route::get('/get-all-videos', [VideoController::class, 'getVideosApi']);
Route::post('/videos/{id}/like', [VideoInteractionController::class, 'toggleLike']);
Route::post('/videos/{id}/share', [VideoInteractionController::class, 'incrementShare']);
Route::post('/videos/{id}/save', [VideoInteractionController::class, 'toggleSave']);
Route::get('/videos/{id}/comments', [CommentController::class, 'getComments']);
Route::post('/videos/comments/{id}', [CommentController::class, 'storeComment']);
Route::get('/products', [ProductController::class, 'getProducts']);
Route::post('customer/favorites/toggle', [FavoriteController::class, 'toggleFavorite']);
Route::get('customer/favorites/count', [FavoriteController::class, 'getFavoriteCount']);
// 🛒 مسارات السلة منفصلة بالتفصيل
Route::get('/cart', [CartController::class, 'index']);          // لعرض محتويات السلة
Route::post('/cart/add', [CartController::class, 'store']);       // لإضافة منتج جديد للسلة
Route::put('/cart/update/{id}', [CartController::class, 'update']); // لتعديل الكمية بداخل السلة
Route::delete('/cart/delete/{id}', [CartController::class, 'destroy']); // لحذف صنف من السلة