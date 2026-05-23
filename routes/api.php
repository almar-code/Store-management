<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\CategorieController;
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

use App\Http\Controllers\Api\VideoInteractionController;

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