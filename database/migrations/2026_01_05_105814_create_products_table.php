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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('image');
            $table->decimal('netPrice', 18, 4)->unsigned();
            $table->decimal('salePrice', 18, 4)->unsigned()->nullable();
            $table->date('salePrice_start')->nullable();
            $table->date('salePrice_end')->nullable();
            $table->string('sku')->nullable();
            $table->integer('quantity')->default(1);
            $table->string('calories')->nullable();
            $table->boolean('is_active');
            $table->boolean('new_arrivals')->default(false);
            $table->bigInteger('brand_id')->unsigned()->nullable();
            $table->bigInteger('category_id')->unsigned();
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
            $table->index('category_id');
            $table->index('brand_id');
            $table->index('is_active');
            $table->index('new_arrivals');
            $table->index('sku');
        });
    }







    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
