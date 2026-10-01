<?php

namespace App\Services;

use App\Models\HeritageItem;

class CartService
{
    /**
     * يطابق سلة الـ Session مع قاعدة البيانات: الأسعار والأسماء الحالية،
     * يحذف القطع التي لم تعد معتمدة/موجودة، ويقصّ الكمية على المخزون المتاح.
     */
    public function refresh(array $cart): array
    {
        if (empty($cart)) {
            return [];
        }

        $items = HeritageItem::whereIn('id', array_keys($cart))
            ->where('status', 'approved')
            ->get()
            ->keyBy('id');

        $fresh = [];

        foreach ($cart as $id => $line) {
            $item = $items->get($id);

            if (! $item || $item->stock < 1) {
                continue;
            }

            $fresh[$id] = [
                'name'     => $item->name,
                'quantity' => min((int) $line['quantity'], $item->stock),
                'price'    => $item->price,
                'image'    => $item->image,
            ];
        }

        if ($fresh !== $cart) {
            session()->put('cart', $fresh);
        }

        return $fresh;
    }
}
