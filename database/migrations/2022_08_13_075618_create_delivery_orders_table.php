<?php

use App\Models\ShNumber;
use App\Models\SoTrading;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(SoTrading::class)->constrained();
            $table->foreignIdFor(ShNumber::class)->constrained();
            $table->date('tanggal');
            $table->string('nomor_do', 50)->unique();
            $table->date('tangga_berlaku');
            $table->integer('kuantitas_kirim');
            $table->integer('kuantitas_diterima');
            $table->text('keterangan')->nullable();
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
        Schema::dropIfExists('delivery_orders');
    }
}
