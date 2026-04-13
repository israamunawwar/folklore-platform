<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
{
    \App\Models\User::create([
        'name' => 'admin',
        'email' => 'admin@admin.com',
        'password' => bcrypt('12345678'),
        'role' => 'admin',
        // أضف أي حقول أخرى للأدمن مثل is_admin إذا كانت موجودة عندك
    ]);
    // إنشاء حساب المدقق
    \App\Models\User::create([
        'name' => 'المدقق',
        'email' => 'moderator@test.com',
        'password' => bcrypt('12345678'),
        'role' => 'moderator',
    ]);

    // إنشاء حساب الناشر
    \App\Models\User::create([
        'name' => 'الناشر الوحيد',
        'email' => 'publisher@test.com',
        'password' => bcrypt('12345678'),
        'role' => 'publisher',
    ]);
 }
}
