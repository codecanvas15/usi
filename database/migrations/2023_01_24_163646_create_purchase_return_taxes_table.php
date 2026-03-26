<?php

use App\Models\PurchaseReturnDetail;
use App\Models\Tax;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseReturnTaxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_return_taxes', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(PurchaseReturnDetail::class)->constrained();
            $table->foreignIdFor(Tax::class);
            $table->decimal('value', 18, 3);
            $table->decimal('amount', 18, 3);
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
        Schema::dropIfExists('purchase_return_taxes');
    }
}
