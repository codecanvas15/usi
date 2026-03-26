<?php

use App\Models\Fleet;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMarineFleetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('marine_fleets', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Fleet::class)->constrained();
            $table->string('name');
            $table->string('nomor_lambung');
            $table->string('panjang');
            $table->string('lebar');
            $table->string('gt');
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
        Schema::dropIfExists('marine_fleets');
    }
}
