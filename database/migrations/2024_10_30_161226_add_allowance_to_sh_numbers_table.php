<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAllowanceToShNumbersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sh_numbers', function (Blueprint $table) {
            $table->decimal('allowance', 18, 3)->default(0)->after('customer_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sh_numbers', function (Blueprint $table) {
            $table->dropColumn('allowance');
        });
    }
}
