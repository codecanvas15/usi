<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCoaIdToStockOpnames extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_opnames', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Coa::class, 'coa_id')->nullable()->after('branch_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_opnames', function (Blueprint $table) {
            $table->dropColumn('coa_id');
        });
    }
}
