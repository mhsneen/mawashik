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
        Schema::create('order_cancel_reason_translations', function (Blueprint $table) {
            $table->id('id');
            $table->bigInteger('order_cancel_reason_id')->unsigned();
            $table->string('locale');
            $table->string('name');
            $table->unique(['order_cancel_reason_id', 'locale'], 'ocr_reason_locale_unique');
            $table->foreign('order_cancel_reason_id')->references('id')->on('order_cancel_reasons')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_cancel_reason_translations');
    }
};
