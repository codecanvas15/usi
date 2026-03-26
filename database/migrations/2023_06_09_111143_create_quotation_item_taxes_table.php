<?php

use App\Models\QuotationItem;
use App\Models\Tax;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotationItemTaxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('qutation_item_taxes');
        Schema::create('quotation_item_taxes', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(QuotationItem::class);
            $table->foreignIdFor(Tax::class);
            $table->decimal('value', 18, 4);
            $table->decimal('total', 18, 3);
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
        Schema::dropIfExists('quotation_item_taxes');
    }
}
