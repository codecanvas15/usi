<?php

use App\Models\Item;
use App\Models\PoTrading;
use App\Models\Price;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePoTradingDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('po_trading_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(PoTrading::class)->constrained();
            $table->foreignIdFor(Item::class)->constrained();
            $table->foreignIdFor(Price::class)->constrained();
            $table->integer('harga');
            $table->mediumInteger('jumlah');
            $table->enum('type', ['L', 'KL']); // L = Liter, KL = Kilo Liter
            $table->text('keterangan');
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
        Schema::dropIfExists('po_trading_details');
    }
}
