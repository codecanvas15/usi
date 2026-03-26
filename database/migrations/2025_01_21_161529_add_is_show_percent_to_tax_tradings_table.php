<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsShowPercentToTaxTradingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tax_tradings', function (Blueprint $table) {
            $table->boolean('is_show_percent')->default(1)->after('value');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tax_tradings', function (Blueprint $table) {
            $table->dropColumn('is_show_percent');
        });
    }
}
