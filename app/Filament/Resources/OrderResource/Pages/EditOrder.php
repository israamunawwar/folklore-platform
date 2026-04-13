<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function afterSave(): void
    {
        $order = $this->record;

        // إذا الحالة "cancelled"
        if ($order->order_status === 'cancelled') {

            // جلب عناصر الطلب
            $items = DB::table('order_items')->where('order_id', $order->id)->get();

            foreach ($items as $item) {
                // زيادة الكمية في جدول المنتجات
                DB::table('heritage_items')
                    ->where('id', $item->heritage_item_id)
                    ->increment('quantity', $item->quantity);
            }

            // اختيارياً: تسجيل لوج للتأكد
            \Log::info("Stock restored for Order #" . $order->id);
        }
    }
}
