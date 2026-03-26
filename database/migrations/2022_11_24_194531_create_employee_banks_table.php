<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeBanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_banks', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Employee::class)->constrained();
            $table->string('bank_name');
            $table->string('behalf_of');
            $table->string('account_number', 24);
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
        Schema::dropIfExists('employee_banks');
    }
}
