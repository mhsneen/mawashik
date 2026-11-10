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
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('delivery_type', ['delivery', 'pickup'])->default('delivery')->after('note');
            $table->decimal('delivery_price', 10, 2)->default(0)->after('delivery_type');
            $table->decimal('distance', 10, 2)->nullable()->after('delivery_price');
            $table->enum('payment_method', ['cash', 'online'])->default('cash')->after('distance');
            $table->foreignId('driver_id')->nullable()->constrained('users')->nullOnDelete()->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['driver_id']);
            $table->dropColumn(['delivery_type', 'delivery_price', 'distance', 'payment_method', 'driver_id']);
        });
    }
};
