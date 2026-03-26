<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCalculationDetailInPurchaseServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_services', function (Blueprint $table) {
            $table->decimal('total_main', 18, 2)->default(0)->after('total');
            $table->decimal('total_additional', 18, 2)->default(0)->after('total_main');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_services', function (Blueprint $table) {
            //
        });
    }
}
