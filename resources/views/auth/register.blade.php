<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>إنشاء حساب</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Tajawal', sans-serif; }</style>
</head>
<body class="bg-stone-100 flex items-center justify-center min-h-screen p-6">
    <div class="max-w-md w-full bg-white shadow-xl rounded-2xl p-8 border border-stone-200">
        <a href="/" class="text-amber-800 font-bold text-sm">← العودة للمتحف</a>
        <h1 class="text-3xl font-bold text-amber-900 my-6 text-center">إنشاء حساب جديد</h1>

        @if($errors->any())
            <div class="p-3 bg-red-100 text-red-700 rounded-lg mb-4 text-sm">
                <ul class="list-disc pr-5 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-stone-700 font-bold mb-2">الاسم</label>
                <input type="text" name="name" value="{{ old('name') }}" required maxlength="255" class="w-full p-3 rounded-xl border border-stone-300 outline-none focus:ring-2 focus:ring-amber-500">
            </div>
            <div>
                <label class="block text-stone-700 font-bold mb-2">البريد الإلكتروني</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="w-full p-3 rounded-xl border border-stone-300 outline-none focus:ring-2 focus:ring-amber-500">
            </div>
            <div>
                <label class="block text-stone-700 font-bold mb-2">كلمة المرور (8 أحرف على الأقل)</label>
                <input type="password" name="password" required minlength="8" class="w-full p-3 rounded-xl border border-stone-300 outline-none focus:ring-2 focus:ring-amber-500">
            </div>
            <div>
                <label class="block text-stone-700 font-bold mb-2">تأكيد كلمة المرور</label>
                <input type="password" name="password_confirmation" required minlength="8" class="w-full p-3 rounded-xl border border-stone-300 outline-none focus:ring-2 focus:ring-amber-500">
            </div>
            <button type="submit" class="w-full bg-amber-800 text-white py-3 rounded-xl font-bold hover:bg-amber-900 transition">إنشاء الحساب</button>
        </form>

        <p class="text-center text-sm text-stone-500 mt-6">عندك حساب؟ <a href="{{ route('login') }}" class="text-amber-800 font-bold">سجّل دخولك</a></p>
    </div>
</body>
</html>
