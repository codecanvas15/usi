<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\PoTrading;
use App\Models\Tax;

class CreatePurchaseOrderAdditionalTaxsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_order_additional_taxs', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Tax::class)->constrained();
            $table->foreignIdFor(PoTrading::class)->constrained('purchase_orders');
            $table->double('value');
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
        Schema::dropIfExists('purchase_order_additional_taxs');
    }
}
