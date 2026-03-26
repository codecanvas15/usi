<?php

use App\Models\InvoiceReturnDetail;
use App\Models\Tax;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceReturnTaxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_return_taxes', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(InvoiceReturnDetail::class)->constrained();
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
        Schema::dropIfExists('invoice_return_taxes');
    }
}
