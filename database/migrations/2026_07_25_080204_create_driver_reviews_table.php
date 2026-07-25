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
        Schema::create('driver_reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id'); // المستخدم الذي كتب التقييم
            $table->unsignedBigInteger('driver_id'); // السائق المُقيَّم
            $table->unsignedBigInteger('order_id');
            $table->tinyInteger('rating')->comment('1-5 Stars');
            $table->text('comment')->nullable(); // تعليق اختياري

            $table->foreign('client_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('driver_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');

            // العميل يقيّم كل طلب مرة وحدة بس
            $table->unique(['order_id', 'client_id']);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_reviews');
    }
};
