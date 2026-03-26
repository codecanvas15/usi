<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateApprovalToLeavesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->foreignId('first_approved_by')->nullable()->constrained('users');
            $table->foreignId('second_approved_by')->nullable()->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leaves', function (Blueprint $table) {
            //
        });
    }
}
