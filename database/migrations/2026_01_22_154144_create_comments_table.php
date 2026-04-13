<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('comments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade'); // مين اللي علق
        $table->foreignId('heritage_item_id')->constrained()->onDelete('cascade'); // على أي قطعة
        $table->text('comment'); // نص التعليق
        $table->integer('rating')->default(5); // التقييم بالنجوم (1-5)
        $table->enum('status', ['pending', 'approved'])->default('pending'); // حالة التعليق (بانتظار الموافقة)
        $table->boolean('is_liked')->default(false); // هل هو معجب بالقطعة (Like)
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
