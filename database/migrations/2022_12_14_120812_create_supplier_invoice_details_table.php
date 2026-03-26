<?php

use App\Models\ItemReceivingReport;
use App\Models\SupplierInvoice;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierInvoiceDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_invoice_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(SupplierInvoice::class)->constrained();
            $table->foreignIdFor(ItemReceivingReport::class)->constrained();
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
        Schema::dropIfExists('supplier_invoice_details');
    }
}
