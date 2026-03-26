<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPurchaseReturnIdToIncomingPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('incoming_payments', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\PurchaseReturn::class, 'purchase_return_id')
                ->after('currency_id')->nullable()->constrained();
        });

        Schema::table('incoming_payment_details', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\PurchaseReturn::class, 'purchase_return_id')
                ->after('incoming_payment_id')->nullable()->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('incoming_payments', function (Blueprint $table) {
            $table->dropForeign('incoming_payments_purchase_return_id_foreign');
            $table->dropColumn('purchase_return_id');
        });

        Schema::table('incoming_payment_details', function (Blueprint $table) {
            $table->dropForeign('incoming_payment_details_purchase_return_id_foreign');
            $table->dropColumn('purchase_return_id');
        });
    }
}
