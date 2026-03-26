<?php

use App\Models\Coa;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierInvoiceGeneralDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_invoice_general_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supplier_invoice_general_id');
            $table->foreign('supplier_invoice_general_id', 'supplier_invoice_general_foreign')->references('id')->on('supplier_invoice_generals');
            $table->foreignIdFor(Coa::class)->constrained();
            $table->decimal('debit', 18, 3);
            $table->decimal('credit', 18, 3);
            $table->enum('type', ['general', 'journal'])->default('general');
            $table->mediumText('notes');
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
        Schema::dropIfExists('supplier_invoice_general_details');
    }
}
