<?php

use App\Models\Item;
use App\Models\Price;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemReceivingReportPurchaseTransportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_receiving_report_purchase_transports', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\ItemReceivingReport::class, 'item_receiving_report_id');
            $table->foreignIdFor(Price::class)->nullable();
            $table->foreignIdFor(Item::class)->nullable();
            $table->decimal('sended', 18, 2)->nullable();
            $table->decimal('received', 18, 2)->nullable();
            $table->decimal('price', 18, 3)->nullable();
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
        Schema::dropIfExists('item_receiving_report_purchase_transports');
    }
}
