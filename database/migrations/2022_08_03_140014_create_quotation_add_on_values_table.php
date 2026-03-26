<?php

use App\Models\Quotation;
use App\Models\QuotationAddOnType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotationAddOnValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotation_add_on_values', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Quotation::class)->constrained();
            $table->foreignIdFor(QuotationAddOnType::class)->constrained();
            $table->string('value', 100);
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
        Schema::dropIfExists('quotation_add_on_values');
    }
}
