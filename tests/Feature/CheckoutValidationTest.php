<?php

namespace Tests\Feature;

use App\Models\HeritageItem;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutValidationTest extends TestCase
{
    use RefreshDatabase;

    private function prepare(): User
    {
        $user = User::factory()->create();
        $item = HeritageItem::create([
            'name' => 'ثوب', 'description' => 'وصف', 'category' => 'clothing',
            'status' => 'approved', 'price' => 10, 'stock' => 5,
        ]);
        $this->actingAs($user)->post(route('cart.add', $item->id));

        return $user;
    }

    public function test_checkout_requires_valid_fields(): void
    {
        $this->prepare();

        $this->post(route('checkout.process'), [])
            ->assertSessionHasErrors(['phone', 'governorate', 'city', 'address_details']);

        $this->assertSame(0, Order::count());
    }

    public function test_phone_must_be_a_syrian_number(): void
    {
        $this->prepare();

        $this->post(route('checkout.process'), [
            'phone' => '12345', 'governorate' => 'دمشق', 'city' => 'المزة', 'address_details' => 'شارع بغداد',
        ])->assertSessionHasErrors('phone');
    }

    public function test_city_must_belong_to_governorate(): void
    {
        $this->prepare();

        $this->post(route('checkout.process'), [
            'phone' => '0933123456', 'governorate' => 'دمشق', 'city' => 'جبلة', 'address_details' => 'شارع بغداد',
        ])->assertSessionHasErrors('city');
    }

    public function test_valid_checkout_creates_order_with_database_price(): void
    {
        $this->prepare();
        HeritageItem::query()->update(['price' => 25]);

        $this->post(route('checkout.process'), [
            'phone' => '0933123456', 'governorate' => 'إدلب', 'city' => 'أريحا', 'address_details' => 'شارع بغداد',
        ])->assertSessionHasNoErrors();

        $this->assertEquals(25, Order::first()->total_price);
    }
}
