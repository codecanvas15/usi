<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKeteranganInPurchaseRequestDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_request_details', function (Blueprint $table) {
            $table->mediumText('keterangan')->nullable()->after('jumlah');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_request_details', function (Blueprint $table) {
            //
        });
    }
}
