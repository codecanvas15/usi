<?php

use App\Models\Employee;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGaragesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('garages', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained();
            $table->string('kode', 24)->unique();
            $table->string('nama');
            $table->string('alamat');
            $table->string('telpon', 24);
            $table->string('longitude', 24);
            $table->string('latitude', 24);
            $table->integer('kapasitas');
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
        Schema::dropIfExists('garages');
    }
}
