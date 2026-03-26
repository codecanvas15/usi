<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCoaIdToStockUsages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_usages', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Coa::class, 'coa_id')->nullable()->after('purchase_request_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_usages', function (Blueprint $table) {
            $table->dropForeign(['coa_id']);
        });
    }
}
