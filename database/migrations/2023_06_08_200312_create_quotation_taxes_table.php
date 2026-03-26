<?php

use App\Models\Quotation;
use App\Models\Tax;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotationTaxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotation_taxes', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Tax::class);
            $table->foreignIdFor(Quotation::class);
            $table->double('value');
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
        Schema::dropIfExists('quotation_taxes');
    }
}
