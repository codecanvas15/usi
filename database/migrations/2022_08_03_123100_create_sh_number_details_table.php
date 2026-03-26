<?php

use App\Models\ShNumber;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShNumberDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sh_number_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ShNumber::class);
            $table->string('alamat');
            $table->string('longitude', 24);
            $table->string('latitude', 24);
            $table->enum('type', ['Supply Point', 'Drop Point']);
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
        Schema::dropIfExists('sh_number_details');
    }
}
