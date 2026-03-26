<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashBondDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cash_bond_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\CashBond::class)->constrained();
            $table->foreignIdFor(\App\Models\Coa::class)->constrained();
            $table->string('type', 20);
            $table->decimal('credit', 18, 3);
            $table->decimal('debit', 18, 3);
            $table->text('note')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('cash_bond_details');
    }
}
