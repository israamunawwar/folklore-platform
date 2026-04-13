<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\HeritageController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\RestaurantInvoiceController;
use App\Models\HeritageItem;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\OrderController;


// --- الصفحة الرئيسية ---
Route::get('/', function () {
    $clothing = HeritageItem::where('status', 'approved')->where('category', 'clothing')->get();
    $tools    = HeritageItem::where('status', 'approved')->where('category', 'tools')->get();
    $food     = HeritageItem::where('status', 'approved')->where('category', 'food')->get();

    return view('welcome', compact('clothing', 'tools', 'food'));
});
   // --- روابط السلة (تعديل الأسلوب ليتوافق مع الفورم) ---
    Route::middleware('auth')->group(function () {
    // غيرنا هاي لـ post لأنها عملية إضافة بيانات
    Route::post('/add-to-cart/{id}', [CartController::class, 'add'])->name('cart.add');

    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');

    // غيرنا هاي لـ delete لأنها عملية حذف (أكثر احترافية)
    Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');

    Route::get('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
});
// --- حل مشكلة التوجيه الذكي (أدمن vs زبون) ---
// هذا الرابط هو الذي يبحث عنه فيلامينت فور تسجيل الدخول
Route::get('/admin/dashboard', function () {
    // إذا كان زبون، اطرده للرئيسية
    if (Auth::check() && Auth::user()->role === 'customer') {
        return redirect('/');
    }

    // إذا كان أدمن أو ناشر، سيتم توجيهه تلقائياً للوحة التحكم بواسطة Filament
    // نحن هنا فقط نمنع الدوامة، لذا سنتركه يكمل لصفحة Dashboard الافتراضية
    return redirect('/admin');

})->name('filament.admin.pages.dashboard')->middleware(['auth']);

// رابط Login للواجهة الأمامية
Route::get('/login', function () {
    return redirect()->route('filament.admin.auth.login');
})->name('login');

Route::post('/logout', function () {
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect('/');
})->name('logout');

//
Route::post('/comments', [CommentController::class, 'store'])->name('comments.store');
//لاظهار التعليقات المقبولة
Route::get('/comments/{itemId}', [CommentController::class, 'getComments']);
//لاظهار عدد الاعجابات
Route::post('/like/{itemId}', [LikeController::class, 'toggle'])->middleware('auth');
//تعديل الكمية لقطعة واحدة في السلة زيادة او نقصان
Route::patch('/cart/update/{id}', [App\Http\Controllers\CartController::class, 'update'])->name('cart.update');

//اتمام عملية الشراء
// صفحة ملخص الطلب وإدخال البيانات
// التعديل هنا: شلنا .index من الاسم عشان يتوافق مع زر السلة
Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout')->middleware('auth');

// معالجة عملية الدفع والطلب
Route::post('/checkout/process', [OrderController::class, 'processCheckout'])->name('checkout.process')->middleware('auth');

// صفحة النجاح بعد الدفع
Route::get('/order/success/{order}', [OrderController::class, 'success'])->name('order.success')->middleware('auth');

// مسار صفحة تفاصيل القطعة
Route::get('/item/{id}', [App\Http\Controllers\HeritageController::class, 'show'])->name('heritage.show');
