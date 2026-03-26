<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuthorizationDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('authorization_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('authorization_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->integer('level')->default(0);
            $table->string('status')->default('draft');
            $table->text('note')->nullable();
            $table->timestamp('status_at')->nullable();
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
        Schema::dropIfExists('authorization_details');
    }
}
