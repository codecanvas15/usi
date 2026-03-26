<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeToTaxTradingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tax_tradings', function (Blueprint $table) {
            $table->string('type')->after('coa_purchase_id')->default('non_ppn');
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
            $table->dropColumn('type');
        });
    }
}
