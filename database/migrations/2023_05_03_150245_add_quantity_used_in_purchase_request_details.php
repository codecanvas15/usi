<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQuantityUsedInPurchaseRequestDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_request_details', function (Blueprint $table) {
            $table->decimal('quantity_used', 10, 2)->default(0)->after('jumlah_diapprove');
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
