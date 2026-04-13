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
       Schema::create('heritage_items', function (Blueprint $table) {
        $table->id();
        $table->string('name'); // اسم القطعة (مثلاً: ثوب فلسطيني)
        $table->text('description'); // وصفها وتاريخها
        $table->string('category'); // تصنيف (ملابس، أدوات، صور)
        $table->string('image')->nullable(); // مسار الصورة
        $table->string('status')->default('pending'); // حالة التدقيق (pending, approved)
        $table->integer('stock')->default(10);
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('heritage_items');
    }
};
