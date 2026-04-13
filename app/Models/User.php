<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property string $role
 */
class User extends Authenticatable
{

    use HasRoles;
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'role',
        'image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(\Filament\Panel $panel): bool
{
    // السماح فقط للأدمن، الناشر، والمدقق بدخول لوحة التحكم
    // أي شخص آخر (سواء كان زبون أو حتى لو كان حقله فارغاً) سيتم منعه وتوجيهه للخارج
    return in_array($this->role, ['admin', 'moderator', 'publisher']);
}

    // هذه الدالة تتحكم في المكان الذي يذهب إليه المستخدم بعد تسجيل الدخول
public function getFilamentRedirectUrl(): ?string
    {
        // إذا كان المستخدم زبون (الرتبة التي نعطيها عند إنشاء الحساب)
        if ($this->role === 'customer') {
            return '/'; // أرسله للصفحة الرئيسية للموقع
        }

        // إذا كان مديراً أو ناشراً أو مدققاً
        return '/admin'; // أرسله لداخل لوحة التحكم
    }


// هذه الدالة تتنفذ تلقائياً عند إنشاء أي مستخدم جديد
protected static function booted()
{
    static::creating(function ($user) {
        // إذا لم يتم تحديد رتبة (وهو ما يحدث عند التسجيل الذاتي)، اجعلها زبون
        if (!$user->role) {
            $user->role = 'customer';
        }
    });
}

}
