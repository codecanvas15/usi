<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('model');
            $table->dropColumn('manufactur');
            $table->dropColumn('reorder_stock');
            $table->dropConstrainedForeignId('item_type_id');
            $table->string('satuan_kirim')->nullable()->after('unit_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('satuan_kirim');
        });
    }
}
