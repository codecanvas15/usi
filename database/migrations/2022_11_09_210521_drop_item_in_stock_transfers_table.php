<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropItemInStockTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_transfers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('item_id');
            $table->dropConstrainedForeignId('price_id');
            $table->dropColumn('qty');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_transfers', function (Blueprint $table) {
            $table->foreignId('item_id')->nullable()->constrained('items');
            $table->foreignId('price_id')->nullable()->constrained('prices');
            $table->decimal('qty', 18)->nullable();
        });
    }
}
