<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsGenerateToBankCodeMutationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bank_code_mutations', function (Blueprint $table) {
            $table->integer('is_generate')->default(1)->after('code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bank_code_mutations', function (Blueprint $table) {
            $table->dropColumn('is_generate');
        });
    }
}
