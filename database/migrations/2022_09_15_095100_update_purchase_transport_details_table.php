<?php

use App\Models\PurchaseTransport;
use App\Models\PurchaseTransportDetail;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePurchaseTransportDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('delivery_orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('purchase_transport_id');
            $table->foreignIdFor(PurchaseTransportDetail::class)->nullable()->after('sh_number_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('delivery_orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('purchase_transport_detail_id');
            $table->foreignIdFor(PurchaseTransport::class)->nullable()->after('sh_number_id')->constrained();
        });
    }
}
