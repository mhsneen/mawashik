<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('admin_notifications', function (Blueprint $table) {
            $table->string('url')->nullable()->after('body');
            $table->string('icon')->nullable()->after('url');
            $table->string('color', 20)->default('primary')->after('icon');
            $table->nullableMorphs('notifiable'); // يبدّل order_id ويشتغل مع أي موديل
        });

        // نقل بيانات order_id القديمة لعمود notifiable قبل حذفه (لو عندك بيانات فعلية بالإنتاج)
        // DB::table('admin_notifications')->whereNotNull('order_id')->update([...]);

        Schema::table('admin_notifications', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->dropColumn('order_id');
        });
    }

    public function down(): void
    {
        Schema::table('admin_notifications', function (Blueprint $table) {
            $table->dropMorphs('notifiable');
            $table->dropColumn(['url', 'icon', 'color']);
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
        });
    }
};
