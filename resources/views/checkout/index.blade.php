<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
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

                <<form action="{{ route('checkout.process') }}" method="POST" class="space-y-4">
    @csrf

    @if(session('error'))
        <div class="p-4 bg-red-100 text-red-700 rounded-lg font-bold mb-4">
            ⚠️ {{ session('error') }}
        </div>
    @endif

    {{-- رقم الهاتف --}}
    <div>
        <label class="block text-stone-700 font-bold mb-2">رقم الهاتف</label>
        <input type="text" name="phone" required class="w-full p-3 rounded-xl border border-stone-300 outline-none focus:ring-2 focus:ring-amber-500" placeholder="09xxxxxxxx">
    </div>

    {{-- المحافظة --}}
    <div>
        <label class="block text-stone-700 font-bold mb-2">المحافظة</label>
        <select id="governorate" name="governorate" required onchange="updateCities()" class="w-full p-3 rounded-xl border border-stone-300 outline-none focus:ring-2 focus:ring-amber-500 bg-white">
            <option value="">-- اختر المحافظة --</option>
            <option value="دمشق">دمشق</option>
            <option value="ريف دمشق">ريف دمشق</option>
            <option value="حلب">حلب</option>
            <option value="حمص">حمص</option>
            <option value="حماة">حماة</option>
            <option value="اللاذقية">اللاذقية</option>
            <option value="طرطوس">طرطوس</option>
            <option value="إدلب">إدلب</option>
            <option value="دير الزور">دير الزور</option>
            <option value="الرقة">الرقة</option>
            <option value="الحسكة">الحسكة</option>
            <option value="درعا">درعا</option>
            <option value="السويداء">السويداء</option>
            <option value="القنيطرة">القنيطرة</option>
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
        <textarea name="address_details" required rows="3" class="w-full p-3 rounded-xl border border-stone-300 outline-none focus:ring-2 focus:ring-amber-500" placeholder="مثال: شارع بغداد، جانب صيدلية الامل، بناية السلام"></textarea>
    </div>

    <button type="submit" class="w-full bg-amber-800 text-white py-4 rounded-xl font-bold text-xl hover:bg-amber-900 transition-all shadow-lg mt-4">
        تأكيد الطلب النهائي
    </button>
</form>

{{-- سكريبت المناطق الذكي --}}
<script>
    const data = {
        "دمشق": ["المزة", "كفرسوسة", "الميدان", "ساروجة", "ركن الدين", "أبو رمانة", "مشروع دمر"],
        "ريف دمشق": ["جرمانا", "صحنايا", "قدسيا", "التل", "النبك", "دوما", "يبرود"],
        "حلب": ["الجميلية", "الحمدانية", "صلاح الدين", "حلب الجديدة", "السريان", "الأعظمية"],
        "حمص": ["الوعر", "الإنشاءات", "المحطة", "باب السباع", "تلكلخ", "الرستن"],
        "اللاذقية": ["المشروع العاشر", "الرمل الشمالي", "جبلة", "القرداحة", "الحفة"],
        "طرطوس": ["المشروع الأول", "صافيتا", "الدريكيش", "بانياس", "الشيخ بدر"],
        "حماة": ["الحاضر", "حي الوادي", "سلمية", "محرده", "مصياف"],
        // يمكنك إضافة بقية المناطق هنا بنفس الطريقة
    };

    function updateCities() {
        const govSelect = document.getElementById('governorate');
        const citySelect = document.getElementById('city');
        const selectedGov = govSelect.value;

        citySelect.innerHTML = '<option value="">-- اختر المنطقة --</option>';

        if (data[selectedGov]) {
            data[selectedGov].forEach(city => {
                let option = document.createElement('option');
                option.value = city;
                option.text = city;
                citySelect.add(option);
            });
        }
    }
</script>

            {{-- القسم الثاني: ملخص الطلب --}}
            <div class="bg-stone-50 p-8 rounded-2xl border border-stone-200 shadow-inner h-fit">
                <h2 class="text-xl font-bold mb-6 text-stone-800 italic underline">ملخص مشترياتك</h2>
                <div class="space-y-4">
                    @php $total = 0; @endphp
                    @foreach(session('cart', []) as $id => $details)
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
</body>
</html>
