<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\HeritageItem;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected string $statusBeforeSave = '';

    protected function beforeSave(): void
    {
        $this->statusBeforeSave = (string) $this->record->getOriginal('order_status');
    }

    protected function afterSave(): void
    {
        $order = $this->record;

        // نرجّع المخزون فقط عند الانتقال إلى "ملغي" لأول مرة
        if ($order->order_status !== 'cancelled' || $this->statusBeforeSave === 'cancelled') {
            return;
        }

        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                HeritageItem::where('id', $item->heritage_item_id)
                    ->increment('stock', $item->quantity);
            }
        });
    }
}
