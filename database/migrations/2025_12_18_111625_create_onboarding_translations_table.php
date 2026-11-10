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
        Schema::create('onboarding_translations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('onboarding_id')->unsigned();
            $table->string('locale');
            $table->string('name');
            $table->string('slug');
            $table->text('details');
            $table->unique(['onboarding_id', 'locale']);
            $table->foreign('onboarding_id')->references('id')->on('onboardings')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('onboarding_translations');
    }
};
