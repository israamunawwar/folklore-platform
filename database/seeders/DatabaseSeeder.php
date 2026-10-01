<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * حسابات الموظفين الأولية. كلمة المرور من SEED_PASSWORD في .env،
     * وفي الإنتاج لا نقبل كلمة مرور افتراضية.
     */
    public function run(): void
    {
        $password = env('SEED_PASSWORD');

        if (! $password) {
            if (app()->isProduction()) {
                $this->command?->error('حدد SEED_PASSWORD في .env قبل تشغيل الـ seeder في الإنتاج.');

                return;
            }

            $password = 'password'; // للتطوير المحلي فقط
        }

        $accounts = [
            ['admin', 'admin@admin.com', 'admin'],
            ['المدقق', 'moderator@test.com', 'moderator'],
            ['الناشر', 'publisher@test.com', 'publisher'],
        ];

        foreach ($accounts as [$name, $email, $role]) {
            $user = User::firstOrNew(['email' => $email]);
            $user->name = $name;
            $user->password = $password;
            $user->role = $role;
            $user->save();
        }
    }
}
