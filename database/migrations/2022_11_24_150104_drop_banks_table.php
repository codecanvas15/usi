<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropBanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_banks', function (Blueprint $table) {
            $table->dropConstrainedForeignId('bank_id');
            $table->string('nama_bank')->after('id');
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->dropConstrainedForeignId('bank_id');
        });

        Schema::table('bank_internals', function (Blueprint $table) {
            $table->dropConstrainedForeignId('bank_id');
            $table->string('nama_bank')->after('id');
        });

        Schema::dropIfExists('banks');
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
