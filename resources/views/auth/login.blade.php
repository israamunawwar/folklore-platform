<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>تسجيل الدخول</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Tajawal', sans-serif; }</style>
</head>
<body class="bg-stone-100 flex items-center justify-center min-h-screen p-6">
    <div class="max-w-md w-full bg-white shadow-xl rounded-2xl p-8 border border-stone-200">
        <a href="/" class="text-amber-800 font-bold text-sm">← العودة للمتحف</a>
        <h1 class="text-3xl font-bold text-amber-900 my-6 text-center">تسجيل الدخول</h1>

        @if($errors->any())
            <div class="p-3 bg-red-100 text-red-700 rounded-lg font-bold mb-4 text-sm">{{ $errors->first() }}</div>
        @endif

        <form action="{{ route('login.attempt') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-stone-700 font-bold mb-2">البريد الإلكتروني</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus class="w-full p-3 rounded-xl border border-stone-300 outline-none focus:ring-2 focus:ring-amber-500">
            </div>
            <div>
                <label class="block text-stone-700 font-bold mb-2">كلمة المرور</label>
                <input type="password" name="password" required class="w-full p-3 rounded-xl border border-stone-300 outline-none focus:ring-2 focus:ring-amber-500">
            </div>
            <label class="flex items-center gap-2 text-sm text-stone-600">
                <input type="checkbox" name="remember" value="1"> تذكّرني
            </label>
            <button type="submit" class="w-full bg-amber-800 text-white py-3 rounded-xl font-bold hover:bg-amber-900 transition">دخول</button>
        </form>

        <p class="text-center text-sm text-stone-500 mt-6">ما عندك حساب؟ <a href="{{ route('register') }}" class="text-amber-800 font-bold">سجّل الآن</a></p>
    </div>
</body>
</html>
