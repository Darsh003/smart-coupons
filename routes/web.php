<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

// ── Products ──────────────────────────────────────────────────
Route::get('/', [ProductController::class, 'index'])->name('products.index');

// ── Cart ──────────────────────────────────────────────────────
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/',          [CartController::class, 'index'])->name('index');
    Route::post('/add',      [CartController::class, 'add'])->name('add');
    Route::patch('/update',  [CartController::class, 'update'])->name('update');
    Route::delete('/remove', [CartController::class, 'remove'])->name('remove');
    Route::delete('/clear',  [CartController::class, 'clear'])->name('clear');
    Route::get('/summary',   [CartController::class, 'summary'])->name('summary');
});

// ── Coupon ────────────────────────────────────────────────────
Route::post('/coupon/apply',   [CouponController::class, 'apply'])->name('coupon.apply');
Route::delete('/coupon/remove',[CouponController::class, 'remove'])->name('coupon.remove');

// ── Order ─────────────────────────────────────────────────────
Route::post('/order/place',                      [OrderController::class, 'place'])->name('order.place');
Route::get('/order/{order}/confirmation',        [OrderController::class, 'confirmation'])->name('order.confirmation');

// ── Auth ──────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',    [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login',   [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegister'])->name('register');
    Route::post('/register',[RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');
