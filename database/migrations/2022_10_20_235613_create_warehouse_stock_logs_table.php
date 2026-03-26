<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarehouseStockLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warehouse_stock_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\WareHouse::class)->constrained();
            $table->foreignIdFor(\App\Models\ItemReceivingReportDetail::class)->constrained();
            $table->decimal('received', 18, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('warehouse_stock_logs');
    }
}
