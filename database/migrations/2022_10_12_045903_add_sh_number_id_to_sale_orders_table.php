<?php

use App\Models\ShNumber;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShNumberIdToSaleOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sale_orders', function (Blueprint $table) {
            $table->foreignIdFor(ShNumber::class)->after('status')->constrained();
        });

        Schema::table('sale_order_details', function (Blueprint $table) {
            $table->unsignedBigInteger('price_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sale_orders', function (Blueprint $table) {
            //
        });
    }
}
