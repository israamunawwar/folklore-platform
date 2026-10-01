<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\HeritageItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentLikeAccessTest extends TestCase
{
    use RefreshDatabase;

    private function item(string $status = 'approved'): HeritageItem
    {
        return HeritageItem::create([
            'name' => 'ثوب', 'description' => 'وصف', 'category' => 'clothing',
            'status' => $status, 'price' => 10, 'stock' => 5,
        ]);
    }

    public function test_pending_items_cannot_be_viewed_added_liked_or_commented(): void
    {
        $user = User::factory()->create();
        $item = $this->item('pending');

        $this->get(route('heritage.show', $item->id))->assertNotFound();
        $this->getJson("/comments/{$item->id}")->assertNotFound();
        $this->actingAs($user)->post(route('cart.add', $item->id))->assertNotFound();
        $this->actingAs($user)->postJson("/like/{$item->id}")->assertNotFound();
        $this->actingAs($user)->post(route('comments.store'), [
            'heritage_item_id' => $item->id, 'comment' => 'x', 'rating' => 5,
        ])->assertNotFound();
    }

    public function test_guest_cannot_post_comments(): void
    {
        $item = $this->item();

        $this->post(route('comments.store'), [
            'heritage_item_id' => $item->id, 'comment' => 'x', 'rating' => 5,
        ])->assertRedirect(route('login'));

        $this->assertSame(0, Comment::count());
    }

    public function test_guest_cannot_like_and_gets_401(): void
    {
        $item = $this->item();

        $this->postJson("/like/{$item->id}")->assertUnauthorized();
    }

    public function test_like_toggles(): void
    {
        $user = User::factory()->create();
        $item = $this->item();

        $this->actingAs($user)->postJson("/like/{$item->id}")->assertJson(['status' => 'liked']);
        $this->actingAs($user)->postJson("/like/{$item->id}")->assertJson(['status' => 'unliked']);
    }

    public function test_comments_json_exposes_only_approved_and_user_name(): void
    {
        $user = User::factory()->create(['email' => 'secret@example.com']);
        $item = $this->item();

        Comment::create(['user_id' => $user->id, 'heritage_item_id' => $item->id, 'comment' => 'ok', 'rating' => 4, 'status' => 'approved']);
        Comment::create(['user_id' => $user->id, 'heritage_item_id' => $item->id, 'comment' => 'hidden', 'rating' => 4, 'status' => 'pending']);

        $response = $this->getJson("/comments/{$item->id}")->assertOk()->assertJsonCount(1);

        $response->assertJsonPath('0.user.name', $user->name);
        $this->assertStringNotContainsString('secret@example.com', $response->getContent());
    }

    public function test_cart_routes_require_login(): void
    {
        $this->patch('/cart/update/1', ['action' => 'increase'])->assertRedirect(route('login'));
        $this->delete('/cart/clear')->assertRedirect(route('login'));
        $this->get('/cart/clear')->assertStatus(405);
    }
}
