<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // hyperpay, paytabs, moyasar, myfatoorah, stripe, paypal, tabby, tamara
            $table->boolean('is_enabled')->default(false);
            $table->enum('mode', ['test', 'live'])->default('test');
            $table->text('config')->nullable(); // مشفّر: يحوي مفاتيح API الخاصة بكل بوابة (JSON)
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
