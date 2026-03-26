<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceGeneralAdditionalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_general_additionals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_general_id');
            $table->foreignIdFor(\App\Models\Item::class)->constrained();
            $table->foreignIdFor(\App\Models\Unit::class)->constrained();
            $table->decimal('quantity', 18, 3);
            $table->decimal('price', 18, 3);
            $table->decimal('sub_total', 18, 3)->default(0);
            $table->decimal('total_tax', 18, 3)->default(0);
            $table->decimal('total', 18, 3)->default(0);
            $table->timestamps();

            $table->foreign('invoice_general_id', 'invg_additional_foreign')->references('id')->on('invoice_generals');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoice_general_additionals');
    }
}
