<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCoaIdToStockUsageDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_usage_details', function (Blueprint $table) {
            $table->foreignId('coa_id')->nullable()->after('price_id')->constrained('coas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_usage_details', function (Blueprint $table) {
            $table->dropColumn('coa_id');
        });
    }
}
