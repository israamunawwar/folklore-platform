<div class="bg-white rounded-xl overflow-hidden shadow-md hover:shadow-2xl transition duration-300 border border-stone-200">
    {{-- عرض الصورة الحقيقية --}}
    <div class="h-56 bg-stone-200 overflow-hidden relative">
        @if($item->image)
            <img src="{{ asset('storage/' . $item->image) }}" class="w-full h-full object-cover">
        @else
            <div class="flex items-center justify-center h-full text-stone-400">لا توجد صورة</div>
        @endif
    </div>

    <div class="p-5">
        <div class="flex justify-between items-start">
            <h2 class="text-xl font-bold text-stone-800">{{ $item->name }}</h2>
            <span class="text-lg font-bold text-amber-700">{{ $item->price }} $</span>
        </div>

        <p class="text-stone-600 mt-2 text-sm line-clamp-2 h-10">
            {{ $item->description }}
        </p>

        {{-- --- قسم التفاعل (لايك وتعليق) --- --}}
        <div class="mt-4 pt-4 border-t border-stone-100 flex items-center gap-6 text-stone-600">
            {{-- أيقونة القلب (Like) --}}
            <button onclick="pressLike(this, {{ $item->id }})"
    class="flex items-center gap-1.5 {{ $item->isLikedBy(auth()->user()) ? 'text-red-500' : 'text-stone-600' }}">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 {{ $item->isLikedBy(auth()->user()) ? 'fill-current' : 'fill-none' }}" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
    </svg>
    <span class="text-sm font-bold like-count">{{ $item->likes_count ?? 0 }}</span>
</button>

            {{-- أيقونة التعليق (Comment) --}}
            <button onclick="openCommentModal({{ $item->id }})" class="flex items-center gap-1.5 hover:text-amber-700 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                <span class="text-sm font-bold">{{ $item->approved_comments_count }}</span>
            </button>
        </div>

        {{-- --- قسم الكمية وزر السلة --- --}}
        <div class="mt-4 flex items-center justify-between">
            <span class="text-xs text-stone-400 font-medium italic">الكمية: {{ $item->stock }}</span>

            <a href="{{ route('cart.add', $item->id) }}"
               class="bg-amber-800 hover:bg-amber-900 text-white px-4 py-2 rounded-lg text-sm font-bold transition flex items-center gap-2 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                إضافة للسلة
            </a>
        </div>
    </div>
</div>
