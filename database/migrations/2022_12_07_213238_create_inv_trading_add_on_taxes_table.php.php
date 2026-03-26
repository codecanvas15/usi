<?php

use App\Models\InvTradingAddOn;
use App\Models\Tax;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvTradingAddOnTaxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inv_trading_add_on_taxes', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(InvTradingAddOn::class)->constrained('inv_trading_add_ons');
            $table->foreignIdFor(Tax::class)->constrained('taxes');
            $table->decimal('value', 18, 4);
            $table->decimal('total', 18, 3);
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
        Schema::dropIfExists('inv_trading_add_on_taxes');
    }
}
