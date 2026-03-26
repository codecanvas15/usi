<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBranchIdToSalariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('salaries', function (Blueprint $table) {
            $table->foreignId('branch_id')->after('id')->nullable()->constrained('branches', 'id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('salaries', function (Blueprint $table) {
            $table->dropForeign('branch_id');
            $table->dropColumn('branch_id');
        });
    }
}
