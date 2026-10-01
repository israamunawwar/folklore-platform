<?php

namespace Tests\Feature;

use App\Models\HeritageItem;
use App\Models\Like;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontTest extends TestCase
{
    use RefreshDatabase;

    private function item(array $attrs = []): HeritageItem
    {
        return HeritageItem::create($attrs + [
            'name' => 'ثوب', 'description' => 'وصف', 'category' => 'clothing',
            'status' => 'approved', 'price' => 10, 'stock' => 5,
        ]);
    }

    public function test_home_lists_only_approved_items_with_like_counts(): void
    {
        $user = User::factory()->create();
        $shown = $this->item(['name' => 'ظاهر']);
        $this->item(['name' => 'مخفي', 'status' => 'pending']);
        Like::create(['user_id' => $user->id, 'heritage_item_id' => $shown->id]);

        $this->actingAs($user)->get('/')
            ->assertOk()
            ->assertSee('ظاهر')
            ->assertDontSee('مخفي');
    }

    public function test_item_page_renders(): void
    {
        $item = $this->item();

        $this->get(route('heritage.show', $item->id))->assertOk()->assertSee('أزياء وحلي تراثية');
    }

    public function test_cart_page_refreshes_prices_and_drops_unavailable_items(): void
    {
        $user = User::factory()->create();
        $kept = $this->item(['name' => 'باقية']);
        $gone = $this->item(['name' => 'ستُحذف']);

        $this->actingAs($user)->post(route('cart.add', $kept->id));
        $this->post(route('cart.add', $gone->id));

        $kept->update(['price' => 99]);
        $gone->update(['status' => 'rejected']);

        $this->get(route('cart.index'))
            ->assertOk()
            ->assertSee('باقية')
            ->assertSee('99')
            ->assertDontSee('ستُحذف');

        $this->assertSame([$kept->id], array_keys(session('cart')));
    }
}
