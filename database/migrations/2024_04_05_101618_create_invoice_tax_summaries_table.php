<?php

use App\Models\Tax;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceTaxSummariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_tax_summaries', function (Blueprint $table) {
            $table->id();
            $table->string('model_class');
            $table->bigInteger('model_id');
            $table->foreignIdFor(Tax::class);
            $table->decimal('tax_value', 18, 2);
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
        Schema::dropIfExists('invoice_tax_summaries');
    }
}
