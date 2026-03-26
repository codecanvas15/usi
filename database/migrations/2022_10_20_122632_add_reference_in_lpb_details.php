<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReferenceInLpbDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_receiving_report_details', function (Blueprint $table) {
            $table->unsignedBigInteger('reference_id')->after('jumlah_diterima');
            $table->string('reference_model')->after('reference_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item_receiving_report_details', function (Blueprint $table) {
            //
        });
    }
}
