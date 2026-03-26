<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVendorIdToJournalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('journals', function (Blueprint $table) {
            $table->bigInteger('vendor_id')->nullable()->after('id');
            $table->bigInteger('customer_id')->nullable()->after('vendor_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('journals', function (Blueprint $table) {
            $table->dropColumn('vendor_id');
            $table->dropColumn('customer_id');
        });
    }
}
