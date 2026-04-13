<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\HeritageItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    // 1. عرض صفحة الدفع (الملخص)
    public function checkout()
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'سلتك فارغة!');
        }
        return view('checkout.index', compact('cart'));
    }

    // 2. معالجة الطلب وخصم المخزن
    public function processCheckout(Request $request)
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'السلة فارغة، لا يمكن إتمام الطلب');
        }

        // حساب المجموع الكلي من السلة
        $total = 0;
        foreach($cart as $id => $details) {
            $total += $details['price'] * $details['quantity'];
        }

        // دمج حقول العنوان الجديدة في نص واحد
        $fullAddress = "المحافظة: " . $request->governorate .
                       " | المنطقة: " . $request->city .
                       " | التفاصيل: " . $request->address_details;

        DB::beginTransaction();
        try {
            // إنشاء الطلب الرئيسي
            $order = Order::create([
                'user_id'      => Auth::id(),
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'total_price'  => $total,
                'phone'        => $request->phone,
                'address'      => $fullAddress,
                'payment_status' => 'pending', // دفع عند الاستلام
                'order_status'   => 'processing',
            ]);

            // إضافة العناصر لجدول OrderItems وخصم المخزن
            foreach ($cart as $id => $details) {
                OrderItem::create([
                    'order_id'         => $order->id,
                    'heritage_item_id' => $id,
                    'quantity'         => $details['quantity'],
                    'price'            => $details['price'],
                ]);

                // خصم الكمية من المستودع
                $product = HeritageItem::findOrFail($id);
                if ($product->stock >= $details['quantity']) {
                    $product->decrement('stock', $details['quantity']);
                } else {
                    throw new \Exception("الكمية المطلوبة من " . $product->name . " غير متوفرة حالياً");
                }
            }

            DB::commit();

            // مسح السلة بعد نجاح العملية
            session()->forget('cart');

            return redirect()->route('order.success', $order->id)->with('success', 'تم تسجيل طلبك بنجاح!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'حدث خطأ أثناء المعالجة: ' . $e->getMessage());
        }
    }

    // 3. صفحة نجاح الطلب
    public function success($orderId)
    {
        // جلب الطلب مع التأكد أن المستخدم الحالي هو صاحب الطلب
        $order = Order::where('user_id', Auth::id())->findOrFail($orderId);

        return view('checkout.success', compact('order'));
    }
}
