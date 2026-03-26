<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInterchangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('interchanges', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('first_item_id');
            $table->unsignedBigInteger('second_item_id');
            $table->boolean('is_change_each_other');
            $table->text('remark')->nullable();
            $table->boolean('is_active');
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
        Schema::dropIfExists('interchanges');
    }
}
