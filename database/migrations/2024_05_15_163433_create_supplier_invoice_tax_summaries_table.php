<?php

use App\Models\Tax;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierInvoiceTaxSummariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_invoice_tax_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\SupplierInvoice::class);
            $table->foreignIdFor(Tax::class);
            $table->decimal('sub_total', 18, 2);
            $table->decimal('tax_value', 18, 5);
            $table->decimal('tax_amount', 18, 2);
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
        Schema::dropIfExists('supplier_invoice_tax_summaries');
    }
}
