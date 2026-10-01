<?php

namespace Tests\Feature;

use App\Filament\Resources\OrderResource\Pages\EditOrder;
use App\Models\HeritageItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OrderCancelTest extends TestCase
{
    use RefreshDatabase;

    private function makeOrder(): array
    {
        $item = HeritageItem::create([
            'name' => 'ثوب', 'description' => 'وصف', 'category' => 'clothing',
            'status' => 'approved', 'price' => 10, 'stock' => 3,
        ]);
        $customer = User::factory()->create();
        $order = Order::create([
            'user_id' => $customer->id, 'order_number' => 'ORD-1', 'total_price' => 20,
            'phone' => '0933123456', 'address' => 'x',
            'payment_status' => 'pending', 'order_status' => 'processing',
        ]);
        OrderItem::create([
            'order_id' => $order->id, 'heritage_item_id' => $item->id, 'quantity' => 2, 'price' => 10,
        ]);

        return [$order, $item];
    }

    private function loginAdmin(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_cancelling_an_order_restores_stock_once(): void
    {
        [$order, $item] = $this->makeOrder();
        $this->loginAdmin();

        Livewire::test(EditOrder::class, ['record' => $order->id])
            ->fillForm(['order_status' => 'cancelled'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame(5, $item->fresh()->stock);

        // حفظ ثانٍ لنفس الطلب الملغي يجب ألا يرجّع المخزون مرة أخرى
        Livewire::test(EditOrder::class, ['record' => $order->id])
            ->fillForm(['phone' => '0933000000'])
            ->call('save');

        $this->assertSame(5, $item->fresh()->stock);
    }
}
