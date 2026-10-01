<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>متحف التراث الرقمي</title>

    {{-- المكتبات الخارجية: Tailwind CSS وخط تجول --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Tajawal', sans-serif; }
    </style>
</head>

<body class="bg-stone-100 text-stone-900">

    {{-- [1] شريط التنقل (Navbar) --}}
    <nav class="bg-stone-900 text-white p-4 sticky top-0 z-50 shadow-md">
        <div class="max-w-6xl mx-auto flex justify-between items-center">

            {{-- القسم الأيمن: الشعار والسلة --}}
            <div class="flex items-center gap-6">
                <a href="/" class="font-bold text-xl text-amber-500">🏠 المتحف</a>

                <a href="{{ route('cart.index') }}" class="relative flex items-center gap-2 hover:text-amber-400 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    {{-- عداد السلة المستند إلى الجلسة --}}
                    <span class="bg-amber-600 text-white text-xs rounded-full px-2 py-0.5 absolute -top-2 -right-2">
                        {{ count(session('cart', [])) }}
                    </span>
                    <span class="hidden md:inline">السلة</span>
                </a>
            </div>

            {{-- القسم الأيسر: حالة المستخدم (تسجيل دخول/خروج) --}}
            <div class="flex gap-4 items-center">
                @auth
                    <div class="flex items-center gap-4">
                        <span class="text-stone-300 italic">مرحباً، {{ auth()->user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm font-bold transition">خروج</button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-amber-500 font-bold hover:text-amber-400">تسجيل الدخول</a>
                    <a href="{{ route('register') }}" class="bg-amber-600 hover:bg-amber-700 text-white px-3 py-1 rounded text-sm font-bold transition">حساب جديد</a>
                @endauth
            </div>
        </div>
    </nav>

    {{-- [2] ترويسة الصفحة (Header) --}}
    <header class="bg-amber-800 text-white py-12 text-center shadow-lg mb-10">
        <h1 class="text-5xl font-bold">متحف التراث الوطني</h1>
        <p class="mt-3 text-amber-100 text-lg">استكشف واقتني أندر القطع التاريخية</p>
    </header>

    {{-- رسائل النظام --}}
    @if(session('success'))
        <div class="max-w-6xl mx-auto mb-6 p-4 bg-green-100 border border-green-200 text-green-800 rounded-xl text-center font-bold">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="max-w-6xl mx-auto mb-6 p-4 bg-red-100 border border-red-200 text-red-800 rounded-xl text-center font-bold">
            ⚠️ {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="max-w-6xl mx-auto mb-6 p-4 bg-red-100 border border-red-200 text-red-800 rounded-xl text-center font-bold">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- [3] المحتوى الرئيسي: عرض التصنيفات والمنتجات --}}
    <main class="max-w-6xl mx-auto p-6 space-y-20">

        @php
            $categories = [
                ['items' => $clothing, 'title' => 'أزياء وحلي تراثية'],
                ['items' => $tools, 'title' => 'كتب وروايات تراثية'],
                ['items' => $food, 'title' => 'أكلات شعبية']
            ];
        @endphp

        @foreach($categories as $cat)
            @if($cat['items']->count() > 0)
            <section>
                {{-- عنوان التصنيف --}}
                <div class="flex items-center mb-8">
                    <div class="h-1 w-12 bg-amber-700 ml-3"></div>
                    <h2 class="text-3xl font-bold text-amber-900">{{ $cat['title'] }}</h2>
                </div>

                {{-- شبكة عرض العناصر (Grid) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($cat['items'] as $item)
                        <div class="bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-2xl transition duration-500 border border-stone-200 flex flex-col">

                            {{-- صورة المنتج --}}
                            <a href="{{ route('heritage.show', $item->id) }}" class="block overflow-hidden h-64">
                                @if($item->image)
                                    <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" class="w-full h-full object-cover hover:scale-110 transition duration-700">
                                @else
                                    <div class="flex items-center justify-center h-full bg-stone-200 text-stone-400">لا توجد صورة</div>
                                @endif
                            </a>

                            {{-- تفاصيل المنتج --}}
                            <div class="p-6 flex-1 flex flex-col">
                                <a href="{{ route('heritage.show', $item->id) }}" class="hover:text-amber-700 transition">
                                    <h3 class="text-xl font-bold text-stone-800">{{ $item->name }}</h3>
                                </a>
                                <p class="text-amber-600 font-bold text-xl my-2">${{ number_format($item->price) }}</p>

                                {{-- مؤشر حالة المخزون --}}
                                @php $availableStock = $item->stock; @endphp
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="w-2 h-2 rounded-full {{ $availableStock > 0 ? 'bg-green-500' : 'bg-red-500 animate-pulse' }}"></span>
                                    <span class="text-xs font-bold {{ $availableStock > 0 ? 'text-stone-500' : 'text-red-500' }}">
                                        {{ $availableStock > 0 ? 'المتوفر: ' . $availableStock . ' قطع' : 'نفدت الكمية مؤقتاً' }}
                                    </span>
                                </div>

                                {{-- وصف المنتج --}}
                                <div class="text-stone-500 text-sm mb-6 leading-relaxed flex-1">
                                    {{ Str::limit($item->description, 90) }}
                                    @if(strlen($item->description) > 90)
                                        <a href="{{ route('heritage.show', $item->id) }}" class="text-amber-700 font-bold text-xs hover:underline block mt-1 italic">
                                            إضغط لعرض المزيد...
                                        </a>
                                    @endif
                                </div>

                                {{-- أزرار التفاعل (تعليق، إعجاب، إضافة للسلة) --}}
                                <div class="flex justify-between items-center border-t border-stone-100 pt-5 mt-auto">
                                    <div class="flex gap-4 items-center">
                                        {{-- زر التعليق --}}
                                        <button onclick="openCommentModal({{ $item->id }})" class="text-stone-500 hover:text-amber-600 flex items-center gap-1 transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                            </svg>
                                            <span class="text-sm font-bold">تعليق</span>
                                        </button>

                                        {{-- زر الإعجاب (Like) --}}
                                        <button onclick="pressLike(this, {{ $item->id }})"
                                            class="flex items-center gap-1 transition {{ in_array($item->id, $likedIds) ? 'text-red-500' : 'text-stone-400 hover:text-red-500' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 {{ in_array($item->id, $likedIds) ? 'fill-current' : '' }}" fill="{{ in_array($item->id, $likedIds) ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                            </svg>
                                            <span class="like-count text-sm font-bold">{{ $item->likes_count }}</span>
                                        </button>
                                    </div>

                                    {{-- زر الإضافة للسلة --}}
                                    <form action="{{ route('cart.add', $item->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" {{ $availableStock <= 0 ? 'disabled' : '' }} class="bg-stone-900 text-white px-4 py-2 rounded-xl text-sm font-bold {{ $availableStock <= 0 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-amber-800 transition-colors duration-300' }}">
                                            {{ $availableStock > 0 ? '🛒 أضف' : 'مباع' }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
            @endif
        @endforeach
    </main>

    {{-- [4] التذييل (Footer) --}}
    <footer class="bg-stone-900 text-stone-500 py-10 mt-20 text-center text-sm border-t border-amber-900/20">
        <p>© 2026 متحف التراث الرقمي - جميع الحقوق محفوظة</p>
    </footer>

    {{-- [5] نافذة مودال التعليقات والتقييم --}}
    <div id="commentModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
        <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl overflow-hidden">

            <div class="p-4 border-b flex justify-between items-center bg-stone-50">
                <h3 class="text-xl font-bold text-stone-800">التعليقات والتقييم</h3>
                <button onclick="closeCommentModal()" class="text-stone-400 hover:text-red-500 text-2xl">&times;</button>
            </div>

            {{-- قائمة التعليقات التي يتم تحميلها عبر Ajax --}}
            <div class="p-6 max-h-[350px] overflow-y-auto space-y-4 text-right" id="commentsList"></div>

            {{-- نموذج إرسال تعليق وتقييم جديد --}}
            <form action="{{ route('comments.store') }}" method="POST" class="p-4 border-t bg-stone-50 space-y-3">
                @csrf
                <input type="hidden" name="heritage_item_id" id="modalItemId">

                <div class="flex items-center gap-3 bg-white p-2 rounded-xl border border-stone-200">
                    <label class="text-sm font-bold text-stone-600 shrink-0">تقييمك:</label>
                    <select name="rating" class="flex-1 outline-none text-amber-600 font-bold bg-transparent cursor-pointer">
                        <option value="5">⭐⭐⭐⭐⭐ (ممتاز)</option>
                        <option value="4">⭐⭐⭐⭐ (جيد جداً)</option>
                        <option value="3">⭐⭐⭐ (جيد)</option>
                        <option value="2">⭐⭐ (مقبول)</option>
                        <option value="1">⭐ (ضعيف)</option>
                    </select>
                </div>

                <div class="flex gap-2">
                    <input type="text" name="comment" placeholder="اكتب تعليقك هنا..." class="flex-1 border rounded-xl px-4 py-2 outline-none focus:ring-2 focus:ring-amber-500" required>
                    <button type="submit" class="bg-amber-800 text-white px-5 py-2 rounded-xl font-bold hover:bg-amber-900 transition">نشر</button>
                </div>
            </form>
        </div>
    </div>

    {{-- [6] البرمجيات النصية (JavaScript) --}}
    <script>
        // وظيفة الإعجاب بالعنصر عبر Fetch API
        function pressLike(btn, itemId) {
            const countSpan = btn.querySelector('.like-count');
            const icon = btn.querySelector('svg');
            fetch('/like/' + itemId, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' }
            })
            .then(res => res.status === 401 ? (alert('يرجى تسجيل الدخول أولاً'), window.location.href='/login') : res.json())
            .then(data => {
                if(!data) return;
                let count = parseInt(countSpan.innerText) || 0;
                if(data.status === 'liked') {
                    btn.classList.add('text-red-500'); icon.setAttribute('fill', 'currentColor'); icon.classList.add('fill-current');
                    countSpan.innerText = count + 1;
                } else {
                    btn.classList.remove('text-red-500'); icon.setAttribute('fill', 'none'); icon.classList.remove('fill-current');
                    countSpan.innerText = Math.max(0, count - 1);
                }
            });
        }
    </script>
    @include('partials.comments-js')
</body>
</html>
