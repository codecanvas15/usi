<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDefaultCoaLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('default_coa_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\DefaultCoa::class)->constrained();
            $table->foreignIdFor(\App\Models\User::class)->nullable()->constrained();
            $table->foreignId('from')->references('id')->on('coas');
            $table->foreignId('to')->references('id')->on('coas');
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
        Schema::dropIfExists('default_coa_logs');
    }
}
