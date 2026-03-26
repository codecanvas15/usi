<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateLaborApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('labor_applications', function (Blueprint $table) {
            $table->string('name')->nullable()->change();
            $table->string('email')->nullable()->change();
            $table->string('address')->nullable()->change();
            $table->string('address_domicil')->nullable()->change();
            $table->string('phone', 24)->nullable()->change();
            $table->date('date_of_birth')->nullable()->change();
            $table->string('place_of_birth')->nullable()->change();
            $table->string('religion')->nullable()->change();
            $table->string('gender', 30)->nullable()->change();
            $table->boolean('marital_status')->nullable()->change();
            $table->string('identity_card_number', 60)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
