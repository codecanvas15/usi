<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashAdvancedReturnTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cash_advanced_return_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('coa_id');
            $table->unsignedBigInteger('cash_advanced_return_id');
            $table->decimal('credit');
            $table->decimal('debit');
            $table->text('description');
            $table->timestamps();

            $table->foreign('coa_id', 'car_transaction_coa_foreign')->references('id')->on('coas');
            $table->foreign('cash_advanced_return_id', 'car_transaction_parent_foreign')->references('id')->on('cash_advanced_returns');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cash_advanced_return_transactions');
    }
}
