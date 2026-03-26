<?php

use App\Models\Item;
use App\Models\Price;
use App\Models\SoTrading;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSoTradingDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('so_trading_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(SoTrading::class)->constrained();
            $table->foreignIdFor(Item::class)->constrained();
            $table->foreignIdFor(Price::class)->constrained();
            $table->integer('harga');
            $table->mediumInteger('jumlah');
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
        Schema::dropIfExists('so_trading_details');
    }
}
