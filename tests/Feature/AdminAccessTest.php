<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\HeritageItem;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role): User
    {
        return User::factory()->create(['role' => $role]);
    }

    private function order(): Order
    {
        return Order::create([
            'user_id' => User::factory()->create()->id, 'order_number' => 'ORD-X', 'total_price' => 10,
            'phone' => '0933123456', 'address' => 'x',
            'payment_status' => 'pending', 'order_status' => 'processing',
        ]);
    }

    private function item(?int $userId = null): HeritageItem
    {
        return HeritageItem::create([
            'name' => 'ثوب', 'description' => 'وصف', 'category' => 'clothing',
            'status' => 'approved', 'price' => 10, 'stock' => 5, 'user_id' => $userId,
        ]);
    }

    public function test_customers_cannot_open_the_panel(): void
    {
        $this->actingAs($this->user('customer'))->get('/admin')->assertForbidden();
    }

    public function test_only_admin_can_open_orders_pages(): void
    {
        $order = $this->order();

        foreach (['publisher', 'moderator'] as $role) {
            $this->actingAs($this->user($role));
            $this->get('/admin/orders')->assertForbidden();
            $this->get("/admin/orders/{$order->id}/edit")->assertForbidden();
        }

        $this->actingAs($this->user('admin'));
        $this->get('/admin/orders')->assertOk();
        $this->get("/admin/orders/{$order->id}/edit")->assertOk();
    }

    public function test_orders_cannot_be_created_manually(): void
    {
        $this->actingAs($this->user('admin'))->get('/admin/orders/create')->assertForbidden();
    }

    public function test_comments_are_moderated_by_admin_and_moderator_only(): void
    {
        $comment = Comment::create([
            'user_id' => $this->user('customer')->id, 'heritage_item_id' => $this->item()->id,
            'comment' => 'x', 'rating' => 5, 'status' => 'pending',
        ]);

        $this->actingAs($this->user('publisher'));
        $this->get('/admin/comments')->assertForbidden();
        $this->get("/admin/comments/{$comment->id}/edit")->assertForbidden();

        $this->actingAs($this->user('moderator'));
        $this->get('/admin/comments')->assertOk();
        $this->get("/admin/comments/{$comment->id}/edit")->assertOk();
    }

    public function test_admin_can_edit_users(): void
    {
        $target = $this->user('moderator');

        $this->actingAs($this->user('admin'))->get("/admin/users/{$target->id}/edit")->assertOk();
        $this->actingAs($this->user('moderator'))->get("/admin/users/{$target->id}/edit")->assertForbidden();
    }

    public function test_admin_cannot_delete_self_or_a_user_with_orders(): void
    {
        $admin = $this->user('admin');
        $order = $this->order();

        $this->assertFalse($admin->can('delete', $admin));
        $this->assertFalse($admin->can('delete', $order->user));
        $this->assertTrue($admin->can('delete', $this->user('publisher')));
    }

    public function test_publisher_can_only_edit_own_items(): void
    {
        $publisher = $this->user('publisher');
        $own = $this->item($publisher->id);
        $other = $this->item($this->user('publisher')->id);

        $this->actingAs($publisher);
        $this->get("/admin/heritage-items/{$own->id}/edit")->assertOk();
        $this->get("/admin/heritage-items/{$other->id}/edit")->assertNotFound();
    }

    public function test_deleting_an_ordered_item_is_a_soft_delete(): void
    {
        $item = $this->item();
        $item->delete();

        $this->assertSoftDeleted($item);
    }
}
