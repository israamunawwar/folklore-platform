<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * أعمدة كانت مستخدمة في الكود بدون migration (قد تكون موجودة أصلاً في قواعد قديمة).
     */
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'image')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('image')->nullable()->after('role');
            });
        }

        if (! Schema::hasColumn('heritage_items', 'user_id')) {
            Schema::table('heritage_items', function (Blueprint $table) {
                // الناشر صاحب القطعة
                $table->foreignId('user_id')->nullable()->after('stock')->constrained()->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('heritage_items', 'deleted_at')) {
            Schema::table('heritage_items', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // enum('pending','approved') لا يسمح بحالة "مرفوض"
        Schema::table('comments', function (Blueprint $table) {
            $table->string('status')->default('pending')->change();
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('heritage_items', 'deleted_at')) {
            Schema::table('heritage_items', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasColumn('heritage_items', 'user_id')) {
            Schema::table('heritage_items', function (Blueprint $table) {
                $table->dropConstrainedForeignId('user_id');
            });
        }

        if (Schema::hasColumn('users', 'image')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('image');
            });
        }
    }
};
