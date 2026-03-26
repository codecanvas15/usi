<?php

use App\Models\Branch;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBranchIdToGpEvaluationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('gp_evaluations', function (Blueprint $table) {
            $table->foreignIdFor(Branch::class)->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('gp_evaluations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('branch_id');
        });
    }
}
