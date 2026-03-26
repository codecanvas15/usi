<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class IncreaseDecimalLostToleranceToInvoiceTradingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice_tradings', function (Blueprint $table) {
            $table->decimal('lost_tolerance', 18, 5)->change();
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
            $table->decimal('lost_tolerance', 18, 3)->change();
        });
    }
}
