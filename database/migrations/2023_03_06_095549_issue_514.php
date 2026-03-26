<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Issue514 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('stock_usage_details');
        Schema::dropIfExists('stock_usages');

        Schema::create('stock_usages', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(\App\Models\WareHouse::class)->nullable()->constrained();
            $table->foreignIdFor(\App\Models\Branch::class)->nullable()->constrained();

            $table->foreignIdFor(\App\Models\Employee::class)->nullable()->constrained();
            $table->foreignIdFor(\App\Models\Division::class)->nullable()->constrained();
            $table->foreignIdFor(\App\Models\Fleet::class)->nullable()->constrained();

            $table->string('fleet_type')->nullable();

            $table->string('code')->nullable();
            $table->date('date')->nullable();
            $table->string('type', 60)->nullable();

            $table->string('note')->nullable();

            $table->string('status')->nullable();
            $table->string('created_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('stock_usage_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\StockUsage::class)->constrained();
            $table->foreignIdFor(\App\Models\Item::class)->constrained();
            $table->foreignIdFor(\App\Models\Unit::class)->constrained();
            $table->foreignIdFor(\App\Models\Price::class)->nullable()->constrained();
            $table->foreignIdFor(\App\Models\ItemReceivingReport::class)->nullable()->constrained();
            $table->foreignIdFor(\App\Models\PoTrading::class)->nullable()->constrained('purchase_orders');
            $table->foreignIdFor(\App\Models\PurchaseOrderGeneral::class)->nullable()->constrained();

            $table->decimal('quantity', 18, 2)->nullable();
            $table->string('necessity')->nullable();
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
        //
    }
}
