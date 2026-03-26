<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangePurchaseTransportDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_transport_details', function (Blueprint $table) {
            $table->decimal('jumlah', 18, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_transport_details', function (Blueprint $table) {
            $table->decimal('jumlah', 18, 2)->change();
        });
    }
}
