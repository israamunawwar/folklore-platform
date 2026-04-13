<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>تم الطلب بنجاح</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Tajawal', sans-serif; }</style>
</head>
<body class="bg-stone-100 flex items-center justify-center min-h-screen p-6">
    <div class="max-w-md w-full bg-white shadow-2xl rounded-3xl p-10 text-center border border-stone-100">
        <div class="text-7xl mb-6 text-green-500">🎉</div>
        <h1 class="text-3xl font-bold text-stone-800 mb-2">شكراً لطلبك!</h1>
        <p class="text-stone-500 mb-8 font-medium">تم تسجيل طلبك في نظامنا بنجاح، وسنتواصل معك قريباً.</p>

        <div class="bg-stone-50 rounded-2xl p-4 mb-8 border border-dashed border-stone-300">
            <span class="block text-sm text-stone-400 font-bold mb-1 uppercase tracking-widest">رقم الطلب</span>
            <span class="text-2xl font-black text-amber-800">#{{ $order->order_number }}</span>
        </div>

        <a href="/" class="block w-full bg-amber-800 text-white py-4 rounded-xl font-bold hover:bg-amber-900 transition-all shadow-lg">
            العودة للرئيسية
        </a>
    </div>
</body>
</html>
