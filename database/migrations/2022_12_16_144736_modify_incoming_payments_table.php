<?php

use App\Models\Project;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyIncomingPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('incoming_payments', function (Blueprint $table) {
            $table->dropColumn('coa_id');
            $table->dropColumn('total');
            $table->foreignIdFor(Project::class)->after('branch_id')->nullable()->constrained();
            $table->string('reject_reason')->after('status');
            $table->string('reference')->after('code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('incoming_payments', function (Blueprint $table) {
            $table->dropColumn('project_id');
            $table->dropColumn('reject_reason');
            $table->dropColumn('reference');
        });
    }
}
