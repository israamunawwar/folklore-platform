<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>إتمام الطلب - الدفع عند الاستلام</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Tajawal', sans-serif; }</style>
</head>
<body class="bg-stone-100 p-6">
    <div class="max-w-5xl mx-auto">
        <h1 class="text-3xl font-bold text-amber-900 mb-8 text-center">🔐 تأكيد تفاصيل الشحن</h1>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

            {{-- القسم الأول: فورم البيانات --}}
            <div class="bg-white shadow-xl rounded-2xl p-8 border border-stone-200">
                <h2 class="text-xl font-bold mb-6 text-stone-800 border-b pb-4">معلومات المستلم</h2>

                @if(session('error'))
                    <div class="p-4 bg-red-100 text-red-700 rounded-lg font-bold mb-4">⚠️ {{ session('error') }}</div>
                @endif

                @if($errors->any())
                    <div class="p-4 bg-red-100 text-red-700 rounded-lg mb-4">
                        <ul class="list-disc pr-5 space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('checkout.process') }}" method="POST" class="space-y-4">
                    @csrf

                    {{-- رقم الهاتف --}}
                    <div>
                        <label class="block text-stone-700 font-bold mb-2">رقم الهاتف</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" required class="w-full p-3 rounded-xl border border-stone-300 outline-none focus:ring-2 focus:ring-amber-500" placeholder="09xxxxxxxx">
                    </div>

                    {{-- المحافظة --}}
                    <div>
                        <label class="block text-stone-700 font-bold mb-2">المحافظة</label>
                        <select id="governorate" name="governorate" required onchange="updateCities()" class="w-full p-3 rounded-xl border border-stone-300 outline-none focus:ring-2 focus:ring-amber-500 bg-white">
                            <option value="">-- اختر المحافظة --</option>
                            @foreach(array_keys(config('locations.governorates')) as $gov)
                                <option value="{{ $gov }}" @selected(old('governorate') === $gov)>{{ $gov }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- المنطقة --}}
                    <div>
                        <label class="block text-stone-700 font-bold mb-2">المنطقة / المدينة</label>
                        <select id="city" name="city" required class="w-full p-3 rounded-xl border border-stone-300 outline-none focus:ring-2 focus:ring-amber-500 bg-white">
                            <option value="">-- اختر المحافظة أولاً --</option>
                        </select>
                    </div>

                    {{-- العنوان التفصيلي --}}
                    <div>
                        <label class="block text-stone-700 font-bold mb-2">العنوان التفصيلي (الشارع / بناية / طابق)</label>
                        <textarea name="address_details" required rows="3" class="w-full p-3 rounded-xl border border-stone-300 outline-none focus:ring-2 focus:ring-amber-500" placeholder="مثال: شارع بغداد، جانب صيدلية الامل، بناية السلام">{{ old('address_details') }}</textarea>
                    </div>

                    <button type="submit" class="w-full bg-amber-800 text-white py-4 rounded-xl font-bold text-xl hover:bg-amber-900 transition-all shadow-lg mt-4">
                        تأكيد الطلب النهائي
                    </button>
                </form>
            </div>

            {{-- القسم الثاني: ملخص الطلب --}}
            <div class="bg-stone-50 p-8 rounded-2xl border border-stone-200 shadow-inner h-fit">
                <h2 class="text-xl font-bold mb-6 text-stone-800 italic underline">ملخص مشترياتك</h2>
                <div class="space-y-4">
                    @php $total = 0; @endphp
                    @foreach($cart as $id => $details)
                        @php $total += $details['price'] * $details['quantity']; @endphp
                        <div class="flex justify-between items-center bg-white p-3 rounded-lg border border-stone-100">
                            <div>
                                <span class="font-bold text-stone-800">{{ $details['name'] }}</span>
                                <span class="text-stone-400 text-sm mr-2">x{{ $details['quantity'] }}</span>
                            </div>
                            <span class="font-bold text-amber-900">{{ $details['price'] * $details['quantity'] }} $</span>
                        </div>
                    @endforeach
                </div>

                <div class="border-t-2 border-dashed border-stone-300 mt-6 pt-4 flex justify-between items-center text-2xl font-bold">
                    <span>الإجمالي:</span>
                    <span class="text-amber-900">{{ $total }} $</span>
                </div>

                <a href="{{ route('cart.index') }}" class="block text-center mt-6 text-stone-500 hover:text-amber-800 font-bold text-sm">
                    ← العودة لتعديل السلة
                </a>
            </div>
        </div>
    </div>

    <script>
        const locations = @json(config('locations.governorates'));
        const oldCity = @json(old('city'));

        function updateCities() {
            const citySelect = document.getElementById('city');
            const cities = locations[document.getElementById('governorate').value] || [];

            citySelect.replaceChildren(new Option('-- اختر المنطقة --', ''));
            cities.forEach(city => citySelect.add(new Option(city, city, false, city === oldCity)));
        }

        updateCities();
    </script>
</body>
</html>
