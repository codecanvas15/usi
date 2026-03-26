<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddTotalQtyToPurchaseTransportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_transports', function (Blueprint $table) {
            $table->decimal('total_qty', 12, 2)->default(0)->after('total');
            $table->decimal('delivered_qty', 12, 2)->default(0)->after('total_qty');
        });

        $purchase_transports = \App\Models\PurchaseTransport::all();
        foreach ($purchase_transports as $key => $purchase_transport) {
            $total_qty = $purchase_transport->purchase_transport_details->map(function ($item) {
                return $item->jumlah * $item->jumlah_do;
            })->sum();
            $delivered_qty = $purchase_transport->delivery_orders
                ->filter(function ($item) {
                    return !in_array($item->status, ['void', 'reject']);
                })
                ->sum('load_quantity');

            DB::table('purchase_transports')
                ->where('id', $purchase_transport->id)
                ->update(['total_qty' => $total_qty, 'delivered_qty' => $delivered_qty]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_transports', function (Blueprint $table) {
            $table->dropColumn(['total_qty', 'delivered_qty']);
        });
    }
}
