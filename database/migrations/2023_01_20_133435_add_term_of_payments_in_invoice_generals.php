<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTermOfPaymentsInInvoiceGenerals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice_generals', function (Blueprint $table) {
            $table->string('term_of_payments')->nullable()->after('due');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoice_generals', function (Blueprint $table) {
            //
        });
    }
}
