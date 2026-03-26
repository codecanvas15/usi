<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMultipleSaleOrdersInInvoiceGenerals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice_generals', function (Blueprint $table) {
            $table->unsignedBigInteger("sale_order_general_id")->nullable()->change();
            $table->boolean('is_old')->default(true);
        });

        Schema::table('invoice_general_details', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\SaleOrderGeneral::class)->nullable()->constrained('sale_order_generals');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoice_generals', function (Blueprint $table) {
            $table->dropColumn('is_old');
        });

        Schema::table('invoice_details', function (Blueprint $table) {
            $table->dropForeign(['sale_order_general_id']);
            $table->dropColumn('sale_order_general_id');
        });
    }
}
