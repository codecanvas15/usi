<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBranchInStockTransfer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_transfers', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Branch::class)->nullable()->after('id')->constrained('branches');
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
            //
        });
    }
}
