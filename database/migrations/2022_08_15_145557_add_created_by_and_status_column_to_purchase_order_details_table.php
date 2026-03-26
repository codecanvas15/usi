<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCreatedByAndStatusColumnToPurchaseOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_details', function (Blueprint $table) {
            $table->foreignId('created_by')->after('keterangan')->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_order_details', function (Blueprint $table) {
            $table->dropConstrainedForeignId('created_by');
        });
    }
}
