<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAttachmentsToInvoiceTradingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice_tradings', function (Blueprint $table) {
            $table->string('attachment')->after('payment_status')->nullable();
        });

        Schema::table('invoice_generals', function (Blueprint $table) {
            $table->string('attachment')->after('payment_status')->nullable();
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
            $table->dropColumn('attachment');
        });

        Schema::table('invoice_generals', function (Blueprint $table) {
            $table->dropColumn('attachment');
        });
    }
}
