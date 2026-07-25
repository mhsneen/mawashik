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
              // ✅ Ledger كامل لكل حركة نقاط (كسب / استبدال) — نفس فلسفة
        // wallet_transactions عندك بالظبط، عشان يكون عندك سجل تاريخي
        // كامل وقابل للتدقيق (audit trail) بدل ما تعتمد بس على الرصيد الحالي.
        Schema::create('loyalty_point_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->enum('type', ['earn', 'redeem', 'refund', 'expire', 'adjustment']);
            // موجبة عند earn/refund، سالبة عند redeem/expire — عشان تقدر
            // تجمعهم مباشرة (SUM) للتحقق من الرصيد وقت الحاجة.
            $table->integer('points');
            $table->unsignedInteger('balance_after');
            $table->string('description')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalty_point_transactions');
    }
};
