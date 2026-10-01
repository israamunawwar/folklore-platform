<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function view(User $user, Order $order): bool
    {
        return $user->role === 'admin';
    }

    // الطلبات تُنشأ من المتجر فقط
    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Order $order): bool
    {
        return $user->role === 'admin';
    }

    // الطلبات لا تُحذف، تُلغى فقط (للحفاظ على السجل المحاسبي)
    public function delete(User $user, Order $order): bool
    {
        return false;
    }
}
