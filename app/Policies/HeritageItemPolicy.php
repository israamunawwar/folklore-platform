<?php

namespace App\Policies;

use App\Models\HeritageItem;
use App\Models\User;

class HeritageItemPolicy
{
    // من يستطيع رؤية القائمة (الكل: مدير، مدقق، ناشر)
    public function viewAny(User $user): bool
{
    // الآدمن والمدقق والناشر.. الكل لازم يشوف الصفحة باللوحة
    return in_array($user->role, ['admin', 'moderator', 'publisher']);
}

    // من يستطيع إضافة قطعة (المدير والناشر فقط)
    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'publisher']);
    }

    // من يستطيع التعديل (الكل، ولكن تذكر أننا قفلنا "الحالة" على الناشر برمجياً)
    public function update(User $user, HeritageItem $heritageItem): bool
    {
        return in_array($user->role, ['admin', 'moderator', 'publisher']);
    }

    // من يستطيع الحذف (المدير فقط - حماية للملفات)
    public function delete(User $user, HeritageItem $heritageItem): bool
    {
        return $user->role === 'admin';
    }
}
