<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClosingPeriodCurrenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('closing_period_currencies', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\ClosingPeriod::class, 'closing_period_id');
            $table->foreignIdFor(\App\Models\Currency::class, 'currency_id')->constrained();
            $table->decimal('exchange_rate', 18, 2);
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
        Schema::dropIfExists('closing_period_currencies');
    }
}
