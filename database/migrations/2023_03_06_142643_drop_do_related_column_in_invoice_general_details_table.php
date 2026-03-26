<?php

use App\Models\DeliveryOrderGeneralDetail;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropDoRelatedColumnInInvoiceGeneralDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice_general_details', function (Blueprint $table) {
            $table->dropForeign('dog_detail_foreign');
            $table->dropColumn('delivery_order_general_detail_id');
            $table->dropColumn('quantity_received');
            $table->dropColumn('quantity_returned');
            $table->dropColumn('quantity_lost');
            $table->dropColumn('quantity_damage');
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
            $table->foreignIdFor(DeliveryOrderGeneralDetail::class);
            $table->decimal('quantity_received', 18, 3)->default(0);
            $table->decimal('quantity_returned', 18, 3)->default(0);
            $table->decimal('quantity_lost', 18, 3)->default(0);
            $table->decimal('quantity_damage', 18, 3)->default(0);
            $table->enum('calculation_type', ['sended','received']);
        });
    }
}
