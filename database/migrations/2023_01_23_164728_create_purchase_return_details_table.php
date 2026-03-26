<?php

use App\Models\Item;
use App\Models\PurchaseReturn;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseReturnDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_return_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(PurchaseReturn::class)->constrained();
            $table->foreignIdFor(Item::class);
            $table->string('reference_model');
            $table->string('reference_id');
            $table->decimal('lpb_qty', 18, 3);
            $table->decimal('qty', 18, 3);
            $table->decimal('return_qty', 18, 3);
            $table->decimal('price', 18, 3);
            $table->decimal('subtotal', 18, 3);
            $table->decimal('tax_amount', 18, 3);
            $table->decimal('total', 18, 3);
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
        Schema::dropIfExists('purchase_return_details');
    }
}
