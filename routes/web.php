<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PublicLandingController;
use App\Http\Controllers\ProductController as PublicProductController;
use App\Http\Controllers\ReviewController;

// ============================
// Public
// ============================
Route::get('/', [PublicLandingController::class, 'index'])->name('landing');
Route::get('/products/{productId}', [PublicProductController::class, 'show'])->name('products.show');

// ============================
// Customer Auth
// ============================
Route::get('/register', [AuthController::class, 'showRegister'])->name('register')->middleware('guest');
Route::post('/register', [AuthController::class, 'register'])->middleware('guest');
Route::get('/login', [AuthController::class, 'showLoginChoice'])->name('login')->middleware('guest');
Route::post('/login/choose', [AuthController::class, 'chooseLogin'])->name('login.choose')->middleware('guest');
Route::get('/login/user', [AuthController::class, 'showUserLogin'])->name('user.login')->middleware('guest');
Route::post('/login/user', [AuthController::class, 'login'])->name('user.login.submit')->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');

// ============================
// Customer Protected
// ============================
Route::middleware('auth')->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{productId}', [CartController::class, 'add'])->name('cart.add');
    Route::put('/cart/{cartId}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{cartId}', [CartController::class, 'remove'])->name('cart.remove');

    Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
    Route::post('/checkout', [OrderController::class, 'placeOrder'])->name('orders.place');
    Route::get('/orders/{orderId}/confirmation', [OrderController::class, 'confirmation'])->name('orders.confirmation');
    Route::get('/my-orders', [OrderController::class, 'myOrders'])->name('orders.index');

    Route::post('/reviews/{productId}', [ReviewController::class, 'store'])->name('reviews.store');
});

// ============================
// Admin Auth
// ============================
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout')->middleware('admin.auth');
});

// ============================
// Admin Protected
// ============================
Route::prefix('admin')->middleware('admin.auth')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    Route::resource('brands', BrandController::class)->names('admin.brands');
    Route::resource('products', ProductController::class)->names('admin.products');

    // Orders
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('admin.orders.index');
    Route::get('/orders/{orderId}', [AdminOrderController::class, 'show'])->name('admin.orders.show');
    Route::post('/orders/{orderId}/status', [AdminOrderController::class, 'updateStatus'])->name('admin.orders.status');

    // Payments
    Route::get('/payments', [PaymentController::class, 'index'])->name('admin.payments.index');
    Route::post('/payments/{orderId}', [PaymentController::class, 'store'])->name('admin.payments.store');
    Route::post('/payments/{paymentId}/paid', [PaymentController::class, 'markPaid'])->name('admin.payments.paid');

    // Reviews
    Route::get('/reviews', [AdminReviewController::class, 'index'])->name('admin.reviews.index');
    Route::post('/reviews/{reviewId}/approve', [AdminReviewController::class, 'approve'])->name('admin.reviews.approve');
    Route::post('/reviews/{reviewId}/reject', [AdminReviewController::class, 'reject'])->name('admin.reviews.reject');

    // Customers
    Route::get('/customers', [CustomerController::class, 'index'])->name('admin.customers.index');
    Route::get('/customers/{userId}', [CustomerController::class, 'show'])->name('admin.customers.show');
});