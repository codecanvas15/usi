<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCloseNoteToPurchaseOrderGeneralsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_generals', function (Blueprint $table) {
            $table->text('close_note')->after('status')->nullable();
        });

        Schema::table('purchase_order_services', function (Blueprint $table) {
            $table->text('close_note')->after('status')->nullable();
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
            $table->dropColumn('close_note');
        });

        Schema::table('purchase_order_services', function (Blueprint $table) {
            $table->dropColumn('close_note');
        });
    }
}
