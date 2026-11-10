<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * توسيع قيم orders.deposit_status لتشمل:
 *  - released:  أُطلق العربون لمحفظة التاجر بعد التسليم.
 *  - forfeited: صودر العربون لصالح المنصة بعد الإلغاء المتأخر.
 *
 * الدوال DepositService::releaseToTrader / forfeitToPlatform تكتب هاتين القيمتين.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement(
                "ALTER TABLE orders MODIFY deposit_status "
                . "ENUM('none','paid','refunded','partial_refund','released','forfeited') "
                . "NOT NULL DEFAULT 'none'"
            );
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::table('orders')->whereIn('deposit_status', ['released', 'forfeited'])
                ->update(['deposit_status' => 'paid']);

            DB::statement(
                "ALTER TABLE orders MODIFY deposit_status "
                . "ENUM('none','paid','refunded','partial_refund') "
                . "NOT NULL DEFAULT 'none'"
            );
        }
    }
};
