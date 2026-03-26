<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashAdvancedReturnDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cash_advanced_return_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cash_advanced_return_id');
            $table->unsignedBigInteger('coa_id');
            $table->unsignedBigInteger('currency_id');
            $table->unsignedBigInteger('reference_id');
            $table->string('reference_model');
            $table->date('date');
            $table->string('transaction_code', 60);
            $table->string('type', 60);
            $table->decimal('exchange_rate', 18, 3)->default(1);
            $table->decimal('amount', 18, 3)->default(0);
            $table->decimal('amount_to_return', 18, 3)->default(0);
            $table->decimal('outstanding_amount', 18, 3)->default(0);
            $table->decimal('balance', 18, 3)->default(0);
            $table->timestamps();

            // foreign
            $table->foreign('coa_id', 'car_detail_id_coa_foreign')->references('id')->on('coas');
            $table->foreign('cash_advanced_return_id', 'car_detail_parent_foreign')->references('id')->on('cash_advanced_returns');
            $table->foreign('currency_id', 'car_detail_currency_id_foreign')->references('id')->on('currencies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cash_advanced_return_details');
    }
}
