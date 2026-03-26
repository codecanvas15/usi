<?php

use App\Models\Item;
use App\Models\InvoiceReturn;
use App\Models\Unit;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceReturnDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_return_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(InvoiceReturn::class)->constrained();
            $table->foreignIdFor(Item::class);
            $table->foreignIdFor(Unit::class);
            $table->string('reference_model');
            $table->string('reference_id');
            $table->decimal('do_qty', 18, 3);
            $table->decimal('qty', 18, 3);
            $table->decimal('return_qty', 18, 3);
            $table->decimal('hpp', 18, 3);
            $table->decimal('hpp_total', 18, 3);
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
        Schema::dropIfExists('invoice_return_details');
    }
}
