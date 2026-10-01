<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $role
 */
class User extends Authenticatable implements FilamentUser
{
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

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    /** أرقام القطع التي أعجب بها المستخدم (استعلام واحد بدل استعلام لكل بطاقة) */
    public function likedItemIds(): array
    {
        return $this->likes()->pluck('heritage_item_id')->all();
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
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
