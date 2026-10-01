<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\HeritageController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

// --- الصفحة الرئيسية وتفاصيل القطعة ---
Route::get('/', [HeritageController::class, 'index'])->name('home');
Route::get('/item/{id}', [HeritageController::class, 'show'])->name('heritage.show');

// --- السلة (كلها للمستخدمين المسجلين) ---
Route::middleware('auth')->prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add/{id}', [CartController::class, 'add'])->name('add');
    Route::patch('/update/{id}', [CartController::class, 'update'])->name('update');
    Route::delete('/remove/{id}', [CartController::class, 'remove'])->name('remove');
    Route::delete('/clear', [CartController::class, 'clear'])->name('clear');
});

// --- الدفع والطلبات ---
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
    Route::post('/checkout/process', [OrderController::class, 'processCheckout'])->name('checkout.process');
    Route::get('/order/success/{order}', [OrderController::class, 'success'])->name('order.success');
});

// --- التعليقات والإعجابات ---
Route::post('/comments', [CommentController::class, 'store'])
    ->middleware(['auth', 'throttle:10,1'])
    ->name('comments.store');
Route::get('/comments/{itemId}', [CommentController::class, 'getComments'])->name('comments.index');
Route::post('/like/{itemId}', [LikeController::class, 'toggle'])
    ->middleware(['auth', 'throttle:60,1'])
    ->name('like.toggle');

// --- تسجيل دخول وتسجيل الزبائن (لوحة الإدارة لها صفحة دخول Filament مستقلة) ---
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1')->name('login.attempt');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1')->name('register.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
