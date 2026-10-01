<?php

namespace App\Http\Controllers;

use App\Models\HeritageItem;
use App\Services\CartService;
use Illuminate\Http\Request;

/**
 * السلة محفوظة في الـ Session فقط ولا تلمس المخزون.
 * الخصم الفعلي من المخزون يتم مرة واحدة داخل OrderController عند إتمام الطلب.
 */
class CartController extends Controller
{
    public function add(Request $request, $id)
    {
        $item = HeritageItem::where('status', 'approved')->findOrFail($id);

        $cart = session()->get('cart', []);
        $currentQty = $cart[$id]['quantity'] ?? 0;

        if ($currentQty + 1 > $item->stock) {
            return redirect()->back()->with('error', 'لا توجد كمية كافية من هذه القطعة.');
        }

        $cart[$id] = [
            'name'     => $item->name,
            'quantity' => $currentQty + 1,
            'price'    => $item->price,
            'image'    => $item->image,
        ];

        session()->put('cart', $cart);

        return redirect()->back()->with('success', 'تمت إضافة القطعة إلى السلة.');
    }

    public function index(CartService $carts)
    {
        $cart = $carts->refresh(session()->get('cart', []));

        return view('cart.index', compact('cart'));
    }

    public function remove(Request $request, $id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return redirect()->back()->with('success', 'تمت إزالة القطعة من السلة.');
    }

    public function clear()
    {
        session()->forget('cart');

        return redirect()->back()->with('success', 'تم إفراغ السلة.');
    }

    public function update(Request $request, $id)
    {
        $request->validate(['action' => 'required|in:increase,decrease']);

        $cart = session()->get('cart', []);

        if (! isset($cart[$id])) {
            return redirect()->back();
        }

        if ($request->action === 'increase') {
            $item = HeritageItem::where('status', 'approved')->findOrFail($id);

            if ($cart[$id]['quantity'] + 1 > $item->stock) {
                return redirect()->back()->with('error', 'نعتذر، لا توجد قطع إضافية في المخزن!');
            }

            $cart[$id]['quantity']++;
        } elseif ($cart[$id]['quantity'] > 1) {
            $cart[$id]['quantity']--;
        } else {
            unset($cart[$id]);
        }

        session()->put('cart', $cart);

        return redirect()->back()->with('success', 'تم تحديث الكمية.');
    }
}
