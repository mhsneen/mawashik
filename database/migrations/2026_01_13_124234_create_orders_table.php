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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->integer('total_items')->unsigned();
            $table->decimal('total_price', 18, 4)->unsigned();
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('final_price', 18, 4)->unsigned();
            $table->foreignId('status_id')->constrained('orderstatuses')->cascadeOnDelete()->default(1);
            $table->foreignId('coupon_id')->nullable()->constrained('discount_codes')->nullOnDelete();
            $table->string('coupon_code')->nullable();

            $table->string('currency')->default('SAR');
            $table->text('note')->nullable();
            $table->decimal('latitude', 11, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->text('address')->nullable();


            $table->boolean('read')->default(false);
            $table->decimal('added_tax', 5, 2)->nullable();
            $table->string('transaction_id')->nullable();
            $table->unsignedBigInteger('cancel_reason_id')->nullable();
            $table->text('cancel_other_reason')->nullable();
            $table->foreign('cancel_reason_id')
                ->references('id')->on('order_cancel_reasons')
                ->onDelete('set null');
            $table->softDeletes();
            $table->timestamps();
            $table->index(['user_id', 'status_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
