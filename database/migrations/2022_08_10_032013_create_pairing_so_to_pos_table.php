<?php

use App\Models\PoTradingDetail;
use App\Models\SoTradingDetail;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePairingSoToPosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pairing_so_to_pos', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(SoTradingDetail::class)->constrained();
            $table->foreignIdFor(PoTradingDetail::class)->constrained();
            $table->integer('alokasi');
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
        Schema::dropIfExists('pairing_so_to_pos');
    }
}
