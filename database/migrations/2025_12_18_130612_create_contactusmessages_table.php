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
        Schema::create('contactusmessages', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_read')->default(false);
            $table->string('name');
            $table->string('mobile');
            $table->string('email');
            $table->string('subject');
            $table->longtext('details')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contactusmessages');
    }
};
