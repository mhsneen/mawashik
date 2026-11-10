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
        Schema::create('textpage_translations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('textpage_id')->unsigned();
            $table->string('locale');
            $table->string('name');
            $table->string('slug');
            $table->longtext('details')->nullable();
            $table->unique(['textpage_id', 'locale']);
            $table->foreign('textpage_id')->references('id')->on('textpages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('textpage_translations');
    }
};
