<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    // إدارة المستخدمين للمدير فقط
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function view(User $user, User $model): bool
    {
        return $user->role === 'admin';
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function update(User $user, User $model): bool
    {
        return $user->role === 'admin';
    }

    // لا يحذف المدير نفسه، ولا مستخدماً لديه طلبات (للحفاظ على السجلات)
    public function delete(User $user, User $model): bool
    {
        return $user->role === 'admin'
            && $user->id !== $model->id
            && ! $model->orders()->exists();
    }

    public function deleteAny(User $user): bool
    {
        return $user->role === 'admin';
    }
}
