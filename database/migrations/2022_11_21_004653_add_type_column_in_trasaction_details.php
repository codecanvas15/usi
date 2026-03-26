<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeColumnInTrasactionDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_details', function (Blueprint $table) {
            $table->enum('item_type', ['additional', 'main'])->default('main')->after('status');
        });

        Schema::table('purchase_order_general_details', function (Blueprint $table) {
            $table->enum('type', ['additional', 'main'])->default('main')->after('harga');
        });

        Schema::table('purchase_order_service_details', function (Blueprint $table) {
            $table->enum('type', ['additional', 'main'])->default('main')->after('harga');
        });
        Schema::table('sale_order_details', function (Blueprint $table) {
            $table->enum('type', ['additional', 'main'])->default('main')->after('status');
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
            //
        });
    }
}
