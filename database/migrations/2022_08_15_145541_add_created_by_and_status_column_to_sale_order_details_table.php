<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCreatedByAndStatusColumnToSaleOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sale_order_details', function (Blueprint $table) {
            $table->foreignId('created_by')->after('type')->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sale_order_details', function (Blueprint $table) {
            $table->dropConstrainedForeignId('created_by');
        });
    }
}
