<?php

namespace App\Policies;

use App\Models\HeritageItem;
use App\Models\User;

class HeritageItemPolicy
{
    private const STAFF = ['admin', 'moderator', 'publisher'];

    public function viewAny(User $user): bool
    {
        return in_array($user->role, self::STAFF);
    }

    public function view(User $user, HeritageItem $item): bool
    {
        return $this->update($user, $item);
    }

    // المدير والناشر فقط ينشئون قطعاً
    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'publisher']);
    }

    // الناشر يعدّل قطعه فقط؛ المدير والمدقق يعدّلان الكل
    public function update(User $user, HeritageItem $item): bool
    {
        if ($user->role === 'publisher') {
            return $item->user_id === $user->id;
        }

        return in_array($user->role, ['admin', 'moderator']);
    }

    // الحذف للمدير فقط (حذف ناعم، فلا تتأثر الطلبات القديمة)
    public function delete(User $user, HeritageItem $item): bool
    {
        return $user->role === 'admin';
    }

    public function deleteAny(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function restore(User $user, HeritageItem $item): bool
    {
        return $user->role === 'admin';
    }

    public function restoreAny(User $user): bool
    {
        return $user->role === 'admin';
    }
}
