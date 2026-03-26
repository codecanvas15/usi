<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateInvoiceTradingsTable2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice_tradings', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Currency::class)->nullable()->after('item_id')->constrained();
            $table->decimal('exchange_rate', 20, 4)->nullable()->after('currency_id');
            $table->string('calculate_from', 60)->nullable()->change();
            $table->string('lost_tolerance_type', 60)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
