<?php

namespace App\Http\Controllers;

use App\Models\HeritageItem; // استدعاء موديل العناصر للتعامل مع قاعدة البيانات
use Illuminate\Http\Request; // استدعاء كلاس Request لاستقبال البيانات المرسلة من الفورم

class CartController extends Controller
{
    /**
     * دالة إضافة منتج إلى السلة مع خصم حقيقي من قاعدة البيانات
     */
    public function add(Request $request, $id)
    {
        // 1. نبحث عن القطعة في قاعدة البيانات
        $item = HeritageItem::findOrFail($id);

        // -- التعديل الجديد: التحقق من المخزن العام قبل أي إجراء --
        if ($item->stock <= 0) {
            return redirect()->back()->with('error', 'نعتذر، هذه القطعة نفدت من المخزن العام!');
        }

        // -- التعديل الجديد: خصم القطعة من قاعدة البيانات فوراً لكي يراها الجميع --
        $item->decrement('stock');

        // 2. نجلب السلة الحالية من الـ Session
        $cart = session()->get('cart', []);

        // 3. نتحقق: هل هذه القطعة موجودة مسبقاً في السلة؟
        if(isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $cart[$id] = [
                "name"     => $item->name,
                "quantity" => 1,
                "price"    => $item->price,
                "image"    => $item->image
            ];
        }

        // 4. تحديث الـ Session
        session()->put('cart', $cart);

        return redirect()->back()->with('success', 'تم حجز القطعة ونقصت من المخزن العام بنجاح!');
    }

    /**
     * دالة عرض صفحة السلة
     */
    public function index()
    {
        $cart = session()->get('cart', []);
        return view('cart.index', compact('cart'));
    }

    /**
     * دالة حذف قطعة وإعادة كميتها لقاعدة البيانات (لأنها لم تُبَع بعد)
     */
    public function remove(Request $request, $id)
    {
        $cart = session()->get('cart');

        if(isset($cart[$id])) {
            // -- التعديل الجديد: إرجاع الكمية المحذوفة للمخزن العام ليراها الزوار الآخرون --
            $item = HeritageItem::find($id);
            if($item) {
                $item->increment('stock', $cart[$id]['quantity']);
            }

            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return redirect()->back()->with('success', 'تم إزالة القطعة وإعادتها للمخزن العام');
    }

    /**
     * دالة إفراغ السلة بالكامل وإعادة كل القطع للمخزن
     */
    public function clear()
    {
        $cart = session()->get('cart', []);

        // -- التعديل الجديد: إرجاع مخزون كل القطع التي كانت في السلة لقاعدة البيانات --
        foreach ($cart as $id => $details) {
            $item = HeritageItem::find($id);
            if ($item) {
                $item->increment('stock', $details['quantity']);
            }
        }

        session()->forget('cart');
        return redirect()->back()->with('success', 'تم إفراغ السلة وإعادة كافة القطع للمخزن');
    }

    /**
     * دالة تحديث الكمية (زيادة أو نقصان) مع مزامنة قاعدة البيانات
     */
    public function update(Request $request, $id)
    {
        $cart = session()->get('cart', []);
        $item = HeritageItem::findOrFail($id);

        if(isset($cart[$id])) {
            // إذا ضغط المستخدم على زر "زيادة"
            if($request->action == 'increase') {
                // نتحقق من قاعدة البيانات مباشرة (Stock)
                if ($item->stock > 0) {
                    $item->decrement('stock'); // نقص من الداتابيز
                    $cart[$id]['quantity']++;
                } else {
                    return redirect()->back()->with('error', 'نعتذر، لا يوجد قطع إضافية في المخزن!');
                }
            }
            // إذا ضغط المستخدم على زر "نقصان"
            elseif($request->action == 'decrease') {
                if($cart[$id]['quantity'] > 1) {
                    $item->increment('stock'); // رجع القطعة للداتابيز
                    $cart[$id]['quantity']--;
                } else {
                    // إذا أصبحت 0، نحذفها ونرجع القطعة للداتابيز
                    $item->increment('stock');
                    unset($cart[$id]);
                }
            }

            session()->put('cart', $cart);
        }

        return redirect()->back()->with('success', 'تم تحديث الكمية والمخزن العام بنجاح');
    }
}
