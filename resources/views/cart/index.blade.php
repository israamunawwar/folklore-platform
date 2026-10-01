<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>سلة المشتريات</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Tajawal', sans-serif; }</style>
</head>
<body class="bg-stone-100 p-6">
    <div class="max-w-4xl mx-auto bg-white shadow-xl rounded-2xl p-8">

        {{-- رسالة النجاح --}}
        @if(session('success'))
        <div class="mb-6 p-4 bg-green-100 border-r-4 border-green-500 text-green-800 shadow-sm rounded-lg flex items-center gap-2 font-bold">
            ✅ {{ session('success') }}
        </div>
        @endif

        {{-- رسالة الخطأ (عند تجاوز المخزون) --}}
        @if(session('error'))
        <div class="mb-6 p-4 bg-red-100 border-r-4 border-red-500 text-red-800 shadow-sm rounded-lg flex items-center gap-2 font-bold animate-bounce">
            ⚠️ {{ session('error') }}
        </div>
        @endif

        {{-- العنوان والتحكم العام --}}
        <div class="flex justify-between items-center mb-8 border-b pb-4">
            <h1 class="text-3xl font-bold text-amber-900">🛒 سلة مشترياتك</h1>
            @if(count($cart) > 0)
                <form action="{{ route('cart.clear') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-500 hover:text-red-700 text-sm font-bold border border-red-200 px-3 py-1 rounded-lg hover:bg-red-50 transition">
                        🗑️ إفراغ السلة
                    </button>
                </form>
            @endif
        </div>

        @if(count($cart) > 0)
            <div class="space-y-6">
                @php $total = 0; @endphp
                @foreach($cart as $id => $details)
                    @php $total += $details['price'] * $details['quantity']; @endphp

                    <div class="flex items-center justify-between border-b pb-6">
                        {{-- معلومات المنتج (يمين) --}}
                        <div class="flex items-center gap-4">
                            @if(!empty($details['image']))
                                <img src="{{ asset('storage/' . $details['image']) }}" alt="{{ $details['name'] }}" class="w-20 h-20 rounded-xl object-cover shadow-sm">
                            @else
                                <div class="w-20 h-20 rounded-xl bg-stone-200"></div>
                            @endif
                            <div>
                                <h3 class="font-bold text-lg text-stone-800">{{ $details['name'] }}</h3>
                                <p class="text-sm text-stone-500">سعر الوحدة: {{ $details['price'] }} $</p>

                                {{-- تصليح رابط الإزالة ليعمل مع نظام DELETE --}}
                                <form action="{{ route('cart.remove', $id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-600 text-xs mt-2 inline-block transition underline">إزالة نهائية</button>
                                </form>
                            </div>
                        </div>

                        {{-- التحكم بالكمية والسعر الإجمالي (يسار) --}}
                        <div class="flex flex-col items-end gap-3">
                            <div class="flex items-center gap-2 bg-stone-100 p-1.5 rounded-xl border border-stone-200">
                                {{-- زر الإنقاص --}}
                                <form action="{{ route('cart.update', $id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="action" value="decrease">
                                    <button type="submit" class="w-8 h-8 flex items-center justify-center bg-white rounded-lg shadow-sm hover:bg-red-500 hover:text-white transition-all font-bold text-stone-600">-</button>
                                </form>

                                <span class="font-bold text-stone-800 px-3 min-w-[30px] text-center text-lg">
                                    {{ $details['quantity'] }}
                                </span>

                                {{-- زر الزيادة --}}
                                <form action="{{ route('cart.update', $id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="action" value="increase">
                                    <button type="submit" class="w-8 h-8 flex items-center justify-center bg-white rounded-lg shadow-sm hover:bg-green-500 hover:text-white transition-all font-bold text-stone-600">+</button>
                                </form>
                            </div>

                            {{-- السعر الكلي لهذا المنتج --}}
                            <p class="font-bold text-xl text-amber-900">
                                {{ $details['price'] * $details['quantity'] }} $
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- ملخص السلة --}}
            <div class="mt-10 p-6 bg-stone-50 rounded-2xl flex justify-between items-center border border-stone-200 shadow-inner">
                <span class="text-2xl font-bold text-stone-700">المجموع الكلي:</span>
                <span class="text-3xl font-bold text-amber-800">{{ $total }} $</span>
            </div>

            {{-- أزرار الأكشن --}}
            <div class="mt-8 flex justify-between items-center">
                <a href="/" class="text-stone-500 hover:text-stone-900 font-bold transition">← العودة للتسوق</a>

                {{-- تصليح الزر ليصبح رابطاً فعالاً --}}
                <a href="{{ route('checkout') }}" class="bg-amber-800 text-white px-10 py-4 rounded-2xl font-bold hover:bg-amber-900 shadow-lg transform hover:scale-105 active:scale-95 transition-all">
                    إتمام عملية الشراء
                </a>
            </div>
        @else
            {{-- حالة السلة فارغة --}}
            <div class="text-center py-20">
                <div class="text-8xl mb-6 opacity-20">🛒</div>
                <p class="text-2xl text-stone-400 font-bold">سلتك فارغة حالياً..</p>
                <a href="/" class="mt-6 inline-block bg-amber-700 text-white px-8 py-3 rounded-xl font-bold hover:bg-amber-800 transition shadow-md">اذهب لاستكشاف التراث</a>
            </div>
        @endif
    </div>
</body>
</html>
