<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCashAdvanceTotalToCashAdvancedReturnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cash_advanced_returns', function (Blueprint $table) {
            $table->decimal('cash_advance_total', 18, 2)->after('amount_total')->nullable();
            $table->decimal('invoice_total', 18, 2)->after('cash_advance_total')->nullable();
            $table->decimal('other_total', 18, 2)->after('invoice_total')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cash_advanced_returns', function (Blueprint $table) {
            $table->dropColumn('cash_advance_total');
            $table->dropColumn('invoice_total');
            $table->dropColumn('other_total');
        });
    }
}
