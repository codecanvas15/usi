<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashBondReturnDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cash_bond_return_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\CashBondReturn::class)->constrained();
            $table->foreignIdFor(\App\Models\CashBond::class)->constrained();
            $table->foreignIdFor(\App\Models\Currency::class)->constrained();
            $table->foreignIdFor(\App\Models\Coa::class)->constrained();

            $table->date('date');
            $table->string('transaction_code', 60);
            $table->string('type', 60);
            $table->decimal('exchange_rate', 18, 3)->default(1);
            $table->decimal('amount', 18, 3)->default(0);
            $table->decimal('amount_to_return', 18, 3)->default(0);
            $table->decimal('outstanding_amount', 18, 3)->default(0);
            $table->decimal('balance', 18, 3)->default(0);
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
        Schema::dropIfExists('cash_bond_return_details');
    }
}
