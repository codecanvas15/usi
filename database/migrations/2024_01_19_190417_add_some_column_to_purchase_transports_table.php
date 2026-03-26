<?php

use App\Models\PoTrading;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSomeColumnToPurchaseTransportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_transports', function (Blueprint $table) {
            $table->foreignIdFor(PoTrading::class)->after('purchase_transport_id')->nullable();
            $table->string('delivery_destination')->after('po_trading_id')->nullable()->default('to_warehouse');
            $table->string('send_from')->after('delivery_destination')->nullable()->default('from_stock');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_transports', function (Blueprint $table) {
            $table->dropForeign('purchase_transports_po_trading_id_foreign');
            $table->dropColumn('po_trading_id');
            $table->dropColumn('delivery_destination');
            $table->dropColumn('send_from');
        });
    }
}
