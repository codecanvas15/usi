<?php

use App\Models\PurchaseOrderGeneral;
use App\Models\Tax;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchseOrderGeneralTaxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_order_general_taxes', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(PurchaseOrderGeneral::class)->constrained();
            $table->foreignIdFor(Tax::class)->constrained();
            $table->decimal('value', 12, 2);
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
        Schema::dropIfExists('purchse_order_general_taxes');
    }
}
