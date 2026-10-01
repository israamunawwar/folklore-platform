<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $item->name }} - تفاصيل القطعة</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Tajawal', sans-serif; }</style>
</head>
<body class="bg-stone-100 text-stone-900">
    <nav class="bg-stone-900 text-white p-4 shadow-md">
        <div class="max-w-6xl mx-auto flex justify-between items-center">
            <a href="/" class="font-bold text-xl text-amber-500">🏠 العودة للمتحف</a>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto p-6 mt-10">
    {{-- رسائل النظام --}}
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-100 border border-green-200 text-green-800 rounded-xl text-center font-bold">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 p-4 bg-red-100 border border-red-200 text-red-800 rounded-xl text-center font-bold">
            ⚠️ {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="mb-6 p-4 bg-red-100 border border-red-200 text-red-800 rounded-xl text-center font-bold">
            {{ $errors->first() }}
        </div>
    @endif

        <div class="bg-white rounded-3xl shadow-xl overflow-hidden grid grid-cols-1 md:grid-cols-2 gap-8">
            {{-- جهة الصورة --}}
            <div class="bg-stone-200 flex items-center justify-center p-4">
                @if($item->image)
                    <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" class="rounded-2xl shadow-lg max-h-[500px] object-cover">
                @else
                    <div class="text-stone-400 py-24">لا توجد صورة</div>
                @endif
            </div>

            {{-- جهة التفاصيل --}}
            <div class="p-8 flex flex-col">
                <div class="flex justify-between items-start mb-2">
                    <span class="text-amber-700 font-bold text-sm underline">تراث / {{ \App\Enums\Category::labelFor($item->category) }}</span>

                    {{-- زر الإعجاب اللي رجعناه --}}
                    <button onclick="pressLike(this, {{ $item->id }})"
                        class="transition {{ auth()->user() && $item->isLikedBy(auth()->user()) ? 'text-red-500' : 'text-stone-400' }}">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="h-8 w-8 {{ auth()->user() && $item->isLikedBy(auth()->user()) ? 'fill-current' : '' }}"
                             fill="{{ auth()->user() && $item->isLikedBy(auth()->user()) ? 'currentColor' : 'none' }}"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                        <span class="like-count text-xs block text-center font-bold">{{ $item->likes()->count() }}</span>
                    </button>
                </div>

                <h1 class="text-4xl font-bold text-stone-800 mb-4">{{ $item->name }}</h1>
                <p class="text-3xl text-amber-600 font-bold mb-6">${{ number_format($item->price) }}</p>

                <div class="border-t border-stone-100 pt-6">
                    <h3 class="font-bold text-lg mb-2 text-stone-700">الوصف التاريخي:</h3>
                    <p class="text-stone-600 leading-relaxed mb-8">{{ $item->description }}</p>
                </div>

                <div class="flex gap-4 mt-auto">
                    <form action="{{ route('cart.add', $item->id) }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full bg-stone-900 text-white py-4 rounded-xl font-bold hover:bg-amber-800 transition shadow-lg text-lg">
                            إضافة إلى السلة 🛒
                        </button>
                    </form>

                    {{-- زر فتح التعليقات --}}
                    <button onclick="openCommentModal({{ $item->id }})" class="bg-amber-100 text-amber-800 px-6 py-4 rounded-xl font-bold hover:bg-amber-200 transition">
                        💬 التعليقات
                    </button>
                </div>
            </div>
        </div>
    </main>

    {{-- تضمين المودال (التعليقات) --}}
    <div id="commentModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm p-4 text-right">
        <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden">
            <div class="p-4 border-b flex justify-between items-center bg-stone-50">
                <h3 class="text-xl font-bold text-stone-800">التعليقات والتقييم</h3>
                <button onclick="closeCommentModal()" class="text-stone-400 hover:text-red-500 transition text-2xl">&times;</button>
            </div>
            <div class="p-6 max-h-[400px] overflow-y-auto" id="commentsList"></div>
            <form action="{{ route('comments.store') }}" method="POST" class="p-4 border-t bg-stone-50">
                @csrf
                <input type="hidden" name="heritage_item_id" id="modalItemId">
                <div class="mb-3 text-center">
                    <select name="rating" class="border rounded px-2 py-1">
                        <option value="5">⭐⭐⭐⭐⭐</option>
                        <option value="4">⭐⭐⭐⭐</option>
                        <option value="3">⭐⭐⭐</option>
                        <option value="2">⭐⭐</option>
                        <option value="1">⭐</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <input type="text" name="comment" placeholder="اكتب تعليقك..." class="flex-1 border rounded-xl px-4 py-2 outline-none focus:ring-2 focus:ring-amber-500" required maxlength="500">
                    <button type="submit" class="bg-amber-800 text-white px-4 py-2 rounded-xl font-bold">نشر</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // وظيفة اللايك (Like)
        function pressLike(btn, itemId) {
            const icon = btn.querySelector('svg');
            const countSpan = btn.querySelector('.like-count');
            fetch('/like/' + itemId, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' }
            })
            .then(res => res.status === 401 ? (alert('سجل دخولك أولاً'), window.location.href='/login') : res.json())
            .then(data => {
                if(!data) return;
                let count = parseInt(countSpan.innerText) || 0;
                if (data.status === 'liked') {
                    btn.classList.add('text-red-500'); icon.setAttribute('fill', 'currentColor');
                    countSpan.innerText = count + 1;
                } else {
                    btn.classList.remove('text-red-500'); icon.setAttribute('fill', 'none');
                    countSpan.innerText = Math.max(0, count - 1);
                }
            });
        }
    </script>
    @include('partials.comments-js')
</body>
</html>
