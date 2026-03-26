<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsIncludeTaxToPurchaseOrderGeneralsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_generals', function (Blueprint $table) {
            $table->integer('is_include_tax')->default(0);
        });

        Schema::table('purchase_order_services', function (Blueprint $table) {
            $table->integer('is_include_tax')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_order_generals', function (Blueprint $table) {
            $table->dropColumn('is_include_tax');
        });

        Schema::table('purchase_order_services', function (Blueprint $table) {
            $table->dropColumn('is_include_tax');
        });
    }
}
