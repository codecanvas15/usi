<?php

use App\Models\DeliveryOrder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemReceivingReportPurchaseTransportDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_receiving_report_purchase_transport_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\ItemReceivingReportPurchaseTransport::class, 'item_receiving_report_purchase_transport_id');
            $table->foreignIdFor(DeliveryOrder::class);
            $table->decimal('sended', 18, 2)->nullable();
            $table->decimal('received', 18, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_receiving_report_purchase_transport_details');
    }
}
