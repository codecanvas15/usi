<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPendingQtyToStockMutationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_mutations', function (Blueprint $table) {
            $table->double('final_stock')->nullable()->after('out');
            $table->double('pending_qty')->nullable()->after('final_stock');
            $table->double('available_qty')->nullable()->after('pending_qty');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_mutations', function (Blueprint $table) {
            $table->dropColumn('final_stock');
            $table->dropColumn('pending_qty');
            $table->dropColumn('available_qty');
        });
    }
}
