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
use App\Http\Controllers\Api\SupabaseWebhookController;
use App\Http\Controllers\Api\UserAddressController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\StripeWebhookController;
use App\Http\Controllers\Api\ManualPaymentController;
use App\Http\Controllers\Api\OrderController;
use Illuminate\Http\Request;


use App\Http\Controllers\Api\AdvertisementController;
use App\Http\Controllers\Api\ProductCommentController;
use App\Http\Controllers\Api\CustomerSyncController;
use App\Http\Controllers\Api\AdvertisementController;



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


Route::post('/webhooks/supabase/profile', [
    SupabaseWebhookController::class,
    'profileUpdated',
]);


// روت جلب جميع الإعلانات النشطة
Route::get('/get-ads', [AdvertisementController::class, 'getAds']);

// روت جلب المنتجات المرتبطة بإعلان عام محدد مع اسم الإعلان
Route::get('/ads/{id}/products', [AdvertisementController::class, 'getGeneralAdProducts']);

Route::get('/products/{p_id}/comments', [ProductCommentController::class, 'getProductComments']);
Route::post('/product-comments', [ProductCommentController::class, 'store']);
Route::delete('/product-comments/{comment_id}', [ProductCommentController::class, 'destroy']);


Route::post('/customers/sync-supabase', [CustomerSyncController::class, 'getCustomerIdBySupabase']);

Route::patch('user-addresses/{id}/set-default', [UserAddressController::class, 'setDefault']);
Route::apiResource('user-addresses', UserAddressController::class);
Route::prefix('payments')->group(function () {
    // Saved cards
    Route::get('/methods', [PaymentController::class, 'index']);

    // Prepare adding a card
    Route::post('/methods/setup', [PaymentController::class, 'setup']);

    // Save successful card locally
    Route::post('/methods/sync', [PaymentController::class, 'sync']);

    // Delete saved card
    Route::delete('/methods/{id}', [PaymentController::class, 'destroy']);

    // Create PaymentIntent
    Route::post('/create-intent', [PaymentController::class, 'createIntent']);
});

Route::post('/checkout/start', [CheckoutController::class, 'start']);
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle']);
Route::get('/store-payment-accounts', [ManualPaymentController::class, 'accounts']);
Route::post('/manual-payment/checkout', [ManualPaymentController::class, 'checkout']);
// يجب وضع هذا المسار خلف middleware تسجيل دخول المسؤول والصلاحيات.
Route::post('/admin/payments/{paymentId}/verify',[ManualPaymentController::class, 'verify']);
// جلب جميع طلبات عميل محدد
Route::get('/orders/{customerId}', [OrderController::class, 'getUserOrders']);

// إلغاء طلب محدد بواسطة رقم الطلب
Route::post('/orders/cancel/{orderId}', [OrderController::class, 'cancelOrder']);
Route::get('/orders/{id}/invoice', [OrderController::class, 'generateInvoice']);
