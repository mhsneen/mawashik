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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->string('title')->nullable();       // مثلاً: المنزل، العمل
            $table->text('address');                    // النص الكامل للعنوان
            $table->decimal('latitude', 11, 8);
            $table->decimal('longitude', 11, 8);

            $table->string('building_number')->nullable();
            $table->string('floor')->nullable();
            $table->string('apartment_number')->nullable();
            $table->text('note')->nullable();            // ملاحظات للسائق

            $table->boolean('is_default')->default(false);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'is_default']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
