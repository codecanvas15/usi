<?php

use App\Models\Employee;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWareHousesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ware_houses', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->mediumText('deskripsi');
            $table->string('is_scrap')->default(false);
            $table->string('jalan');
            $table->string('kota');
            $table->string('provinsi');
            $table->string('country');
            $table->string('zip_code');
            $table->foreignIdFor(Employee::class)->constrained();
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
        Schema::dropIfExists('ware_houses');
    }
}
