<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceGeneralCoasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_general_coas', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\InvoiceGeneral::class)->constrained();
            $table->foreignIdFor(\App\Models\Coa::class)->constrained();
            $table->string('type');
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
        Schema::dropIfExists('invoice_general_coas');
    }
}
