<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemReceivingReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_receiving_reports', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 24);
            $table->string('tipe', 24);
            $table->string('reference_model', 120);
            $table->string('status', 60);
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
        Schema::dropIfExists('item_receiving_reports');
    }
}
