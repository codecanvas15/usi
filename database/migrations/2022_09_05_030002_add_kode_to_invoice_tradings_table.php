<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKodeToInvoiceTradingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice_tradings', function (Blueprint $table) {
            $table->string('kode', 50)->after('so_trading_id');
            $table->decimal('jumlah', 18, 2)->nullable()->change();
            $table->decimal('sub_total', 18, 2)->nullable()->change();
            $table->decimal('total', 18, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoice_tradings', function (Blueprint $table) {
            $table->dropColumn('kode');
            $table->decimal('jumlah', 18, 2)->change();
            $table->decimal('sub_total', 18, 2)->change();
            $table->decimal('total', 18, 2)->change();
        });
    }
}
