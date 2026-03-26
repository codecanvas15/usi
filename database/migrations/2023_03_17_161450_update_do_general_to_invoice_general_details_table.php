<?php

use App\Models\DeliveryOrderGeneral;
use App\Models\DeliveryOrderGeneralDetail;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDoGeneralToInvoiceGeneralDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice_general_details', function (Blueprint $table) {
            $table->foreignIdFor(DeliveryOrderGeneral::class)->constrained();
            $table->dropColumn('delivery_order_general_detail_id');
            $table->dropColumn('calculation_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoice_general_details', function (Blueprint $table) {
            $table->dropConstrainedForeignId('delivery_order_general_id');
            $table->foreignIdFor(DeliveryOrderGeneralDetail::class)->constrained();
            $table->enum('calculation_type', ['sended','received']);
        });
    }
}
