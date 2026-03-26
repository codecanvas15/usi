<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryOrderGeneralsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_order_generals', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Branch::class)->constrained();
            $table->foreignIdFor(\App\Models\SaleOrderGeneral::class)->constrained();
            $table->foreignIdFor(\App\Models\Customer::class)->constrained();
            $table->foreignIdFor(\App\Models\Vendor::class)->nullable()->constrained();
            $table->string('code', 60);
            $table->date('date');
            $table->date('target_delivery')->nullable();
            $table->string('supply');
            $table->string('drop');
            $table->text('description')->nullable();
            $table->string('status', 60);
            $table->boolean('is_invoice_created')->default(false);
            $table->softDeletes();
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
        Schema::dropIfExists('delivery_order_generals');
    }
}
