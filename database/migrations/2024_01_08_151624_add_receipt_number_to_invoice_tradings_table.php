<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReceiptNumberToInvoiceTradingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice_tradings', function (Blueprint $table) {
            $table->string('receipt_number')->after('kode')->nullable();
        });

        Schema::table('invoice_generals', function (Blueprint $table) {
            $table->string('receipt_number')->after('code')->nullable();
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->string('short_name')->after('name')->nullable();
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
            $table->dropColumn('receipt_number');
        });

        Schema::table('invoice_generals', function (Blueprint $table) {
            $table->dropColumn('receipt_number');
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('short_name');
        });
    }
}
