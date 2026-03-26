<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQuantityApprovedInPurchaseRequestDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_request_details', function (Blueprint $table) {
            $table->integer('jumlah_diapprove')->default(0)->after('jumlah');
            $table->string('approve_desc')->nullable()->after('keterangan');
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
            $table->dropColumn(['jumlah_diapprove', 'approve_desc']);
        });
    }
}
