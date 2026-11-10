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
        Schema::create('discount_code_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('discount_code_id')->constrained('discount_codes')->cascadeOnDelete();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('discount_percentage');
            $table->decimal('discount_amount', 10, 2);
            $table->dateTime('used_at');
            $table->timestamps();
        });
    }
        /*

id	معرف السجل
discount_code_id	الكوبون المستخدم → مرتبط بجدول discount_codes
order_id	الطلب الذي استخدم فيه الكوبون (nullable)
user_id	المستخدم الذي استخدم الكوبون
discount_percentage	نسبة الخصم على هذا الاستخدام
discount_amount	المبلغ الفعلي الذي تم خصمه
used_at	وقت الاستخدام
timestamps	created_at و updated_at
        */
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_code_usages');
    }
};
