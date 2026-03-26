<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDivisionToSomeTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Division::class)->nullable()->after('branch_id');
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Division::class)->nullable()->after('branch_id');

            $table->dropColumn('divisi');
        });

        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Division::class)->nullable()->after('branch_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
