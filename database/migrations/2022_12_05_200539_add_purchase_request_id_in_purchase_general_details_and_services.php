<?php

use App\Models\PurchaseRequestDetail;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPurchaseRequestIdInPurchaseGeneralDetailsAndServices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_general_details', function (Blueprint $table) {
            $table->foreignIdFor(PurchaseRequestDetail::class)->nullable()->after('purchase_order_general_id');
        });

        Schema::table('purchase_order_service_details', function (Blueprint $table) {
            $table->foreignIdFor(PurchaseRequestDetail::class)->nullable()->after('purchase_order_service_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_general_details', function (Blueprint $table) {
            //
        });
    }
}
