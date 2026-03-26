<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateNoRekeningToBankInternalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bank_internals', function (Blueprint $table) {
            $table->dropColumn('no_rekening');
        });
        Schema::table('bank_internals', function (Blueprint $table) {
            $table->string('no_rekening', 60)->unique()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bank_internals', function (Blueprint $table) {
            //
        });
    }
}
