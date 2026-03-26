<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMoreOnEmployeeTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->json("current_residential_address")->nullable()->after("alamat_domisili");
            $table->json("parents_residence_address")->nullable()->after("current_residential_address");
            $table->string("postal_code", 24)->nullable()->after("alamat");
            $table->string("house_phone", 24)->nullable()->after("nomor_telepone");
            $table->string("occupied_house")->nullable()->after("house_phone");
            $table->string("vehicle_ownership")->nullable()->after("vehicle");
            $table->json("vehicle_details")->nullable()->after("vehicle_ownership");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("employees", function (Blueprint $table) {
            $table->dropColumn("current_residential_address");
            $table->dropColumn("parents_residence_address");
            $table->dropColumn("postal_code");
            $table->dropColumn("house_phone");
            $table->dropColumn("occupied_house");
            $table->dropColumn("vehicle_ownership");
            $table->dropColumn("vehicle_details");
        });
    }
}
