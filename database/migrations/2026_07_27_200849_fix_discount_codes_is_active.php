<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
      public function up(): void
    {
        // عكس القيم في قاعدة البيانات: 1 يصبح 0، و 0 يصبح 1
        DB::table('discount_codes')->update([
            'is_active' => DB::raw('CASE WHEN is_active = 1 THEN 0 ELSE 1 END')
        ]);
    }

    public function down(): void
    {
        DB::table('discount_codes')->update([
            'is_active' => DB::raw('CASE WHEN is_active = 1 THEN 0 ELSE 1 END')
        ]);
    }
};
