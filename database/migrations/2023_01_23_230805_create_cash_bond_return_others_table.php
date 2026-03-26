<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashBondReturnOthersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cash_bond_return_others', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\CashBondReturn::class)->constrained();
            $table->foreignIdFor(\App\Models\Coa::class)->constrained();
            $table->decimal('amount', 18, 3);
            $table->text('description');
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
        Schema::dropIfExists('cash_bond_return_others');
    }
}
