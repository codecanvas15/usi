<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSupplierIdToPoTradingsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('po_tradings', function (Blueprint $table) {
            // $table->foreignIdFor(Supplier::class)->after('total')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('po_tradings', function (Blueprint $table) {
            // $table->dropConstrainedForeignId('supplier_id');
            // $table->dropColumn('supplier_id');
        });
    }
}
