<?php

namespace Tests\Feature;

use App\Models\HeritageItem;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutStockTest extends TestCase
{
    use RefreshDatabase;

    private function item(int $stock, string $status = 'approved'): HeritageItem
    {
        return HeritageItem::create([
            'name' => 'ثوب',
            'description' => 'وصف',
            'category' => 'clothing',
            'status' => $status,
            'price' => 10,
            'stock' => $stock,
        ]);
    }

    private function checkoutData(): array
    {
        return [
            'phone' => '0933123456',
            'governorate' => 'دمشق',
            'city' => 'المزة',
            'address_details' => 'شارع بغداد',
        ];
    }

    public function test_adding_to_cart_does_not_touch_stock(): void
    {
        $user = User::factory()->create();
        $item = $this->item(5);

        $this->actingAs($user)->post(route('cart.add', $item->id))->assertSessionHas('success');

        $this->assertSame(5, $item->fresh()->stock);
    }

    public function test_checkout_decrements_stock_exactly_once(): void
    {
        $user = User::factory()->create();
        $item = $this->item(5);

        $this->actingAs($user)->post(route('cart.add', $item->id));
        $this->actingAs($user)->post(route('cart.add', $item->id));
        $this->post(route('checkout.process'), $this->checkoutData())->assertSessionHasNoErrors();

        $this->assertSame(3, $item->fresh()->stock);
        $this->assertSame(1, Order::count());
    }

    public function test_last_item_can_be_purchased(): void
    {
        $user = User::factory()->create();
        $item = $this->item(1);

        $this->actingAs($user)->post(route('cart.add', $item->id));
        $this->post(route('checkout.process'), $this->checkoutData());

        $this->assertSame(0, $item->fresh()->stock);
        $this->assertSame(1, Order::count());
    }

    public function test_cannot_add_more_than_available_stock(): void
    {
        $user = User::factory()->create();
        $item = $this->item(1);

        $this->actingAs($user)->post(route('cart.add', $item->id));
        $this->post(route('cart.add', $item->id))->assertSessionHas('error');

        $this->assertSame(1, session('cart')[$item->id]['quantity']);
    }

    public function test_cannot_checkout_more_than_available_stock(): void
    {
        $user = User::factory()->create();
        $item = $this->item(2);

        $this->actingAs($user)->post(route('cart.add', $item->id));
        $item->update(['stock' => 0]);
        $this->post(route('checkout.process'), $this->checkoutData())->assertSessionHas('error');

        $this->assertSame(0, Order::count());
        $this->assertSame(0, $item->fresh()->stock);
    }
}
