<?php

namespace App\Http\Controllers;

use App\Exceptions\InsufficientStockException;
use App\Models\HeritageItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

    // 2. معالجة الطلب: الخصم الوحيد من المخزون، والأسعار من قاعدة البيانات
    public function processCheckout(Request $request)
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'السلة فارغة، لا يمكن إتمام الطلب');
        }

        $fullAddress = "المحافظة: " . $request->governorate .
                       " | المنطقة: " . $request->city .
                       " | التفاصيل: " . $request->address_details;

        try {
            $order = DB::transaction(function () use ($cart, $request, $fullAddress) {
                $total = 0;
                $lines = [];

                // قفل القطع بترتيب ثابت (id) لتفادي deadlock بين طلبين متزامنين
                $ids = collect(array_keys($cart))->sort()->values();

                foreach ($ids as $id) {
                    $quantity = $cart[$id]['quantity'];

                    $product = HeritageItem::where('id', $id)
                        ->where('status', 'approved')
                        ->lockForUpdate()
                        ->first();

                    if (! $product) {
                        throw new InsufficientStockException('إحدى القطع في سلتك لم تعد متاحة، يرجى مراجعة السلة.');
                    }

                    if ($product->stock < $quantity) {
                        throw new InsufficientStockException("الكمية المطلوبة من {$product->name} غير متوفرة حالياً");
                    }

                    $total += $product->price * $quantity;
                    $lines[] = [$product, $quantity];
                }

                $order = Order::create([
                    'user_id'        => Auth::id(),
                    'order_number'   => 'ORD-' . strtoupper(uniqid()),
                    'total_price'    => $total,
                    'phone'          => $request->phone,
                    'address'        => $fullAddress,
                    'payment_status' => 'pending', // دفع عند الاستلام
                    'order_status'   => 'processing',
                ]);

                foreach ($lines as [$product, $quantity]) {
                    OrderItem::create([
                        'order_id'         => $order->id,
                        'heritage_item_id' => $product->id,
                        'quantity'         => $quantity,
                        'price'            => $product->price,
                    ]);

                    $product->decrement('stock', $quantity);
                }

                return $order;
            });
        } catch (InsufficientStockException $e) {
            return redirect()->route('cart.index')->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            Log::error('Checkout failed: ' . $e->getMessage(), ['user_id' => Auth::id()]);
            return back()->with('error', 'حدث خطأ أثناء معالجة الطلب، حاول مرة أخرى.');
        }

        session()->forget('cart');

        return redirect()->route('order.success', $order->id)->with('success', 'تم تسجيل طلبك بنجاح!');
    }

    // 3. صفحة نجاح الطلب (للمالك فقط)
    public function success($orderId)
    {
        $order = Order::where('user_id', Auth::id())->findOrFail($orderId);

        return view('checkout.success', compact('order'));
    }
}
