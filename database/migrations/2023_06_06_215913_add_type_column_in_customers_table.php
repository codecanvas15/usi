<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddTypeColumnInCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('type', 30)->nullable();
        });

        $purchaseOrders = DB::table('purchase_orders')->get();
        $purchaseOrderCustomerIds = $purchaseOrders->pluck('customer_id')->toArray();

        \App\Models\Customer::whereIn('id', $purchaseOrderCustomerIds)->update([
            'type' => 'trading',
        ]);

        \App\Models\Customer::whereNotIn('id', $purchaseOrderCustomerIds)->update([
            'type' => 'general',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            //
        });
    }
}
