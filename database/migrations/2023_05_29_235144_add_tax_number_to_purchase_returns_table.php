<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTaxNumberToPurchaseReturnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_returns', function (Blueprint $table) {
            $table->string('tax_number')->nullable()->after('vendor_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_returns', function (Blueprint $table) {
            $table->dropColumn('tax_number');
        });
    }
}
