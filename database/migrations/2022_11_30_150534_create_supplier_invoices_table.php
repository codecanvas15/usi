<?php

use App\Models\Currency;
use App\Models\Vendor;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Vendor::class)->constrained();
            $table->foreignIdFor(Currency::class)->constrained();
            $table->text('item_receiving_report_id')->nullable();
            $table->string('reference'); // nomor faktur
            $table->date('date');
            $table->enum('status', ['paid', 'unpaid', 'partial'])->default('unpaid');
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
        Schema::dropIfExists('supplier_invoices');
    }
}
