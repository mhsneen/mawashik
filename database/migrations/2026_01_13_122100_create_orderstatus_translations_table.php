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
        Schema::create('orderstatus_translations', function (Blueprint $table) {
            $table->id('id');
            $table->bigInteger('orderstatus_id')->unsigned();
            $table->string('locale');
            $table->string('name');
            $table->unique(['orderstatus_id', 'locale']);
            $table->foreign('orderstatus_id')->references('id')->on('orderstatuses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orderstatus_translations');
    }
};
