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
      Schema::create('admin_notifications', function (Blueprint $table) {
    $table->id();
    $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
    $table->string('type'); // new_order_cash, order_paid_online
    $table->string('title');
    $table->text('body')->nullable();
    $table->boolean('is_read')->default(false);
    $table->timestamp('read_at')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_notifications');
    }
};
