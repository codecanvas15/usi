<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateJournalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('journals', function (Blueprint $table) {
            // $table->unsignedBigInteger('currency_id')->nullable()->change();
            $table->decimal('exchange_rate', 18, 3)->nullable()->change();
            $table->boolean('is_generated')->default(false)->after('reference_id');
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
