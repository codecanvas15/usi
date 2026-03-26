<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRevaluationLogToStockMutationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_mutations', function (Blueprint $table) {
            $table->text('revaluation_log')->nullable()->after('tax');
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
            $table->dropColumn('revaluation_log');
        });
    }
}
