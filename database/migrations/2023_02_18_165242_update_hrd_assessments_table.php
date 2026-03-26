<?php

use App\Models\Branch;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateHrdAssessmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hrd_assessments', function (Blueprint $table) {
            $table->foreignIdFor(Branch::class)->nullable()->constrained();
            $table->dropConstrainedForeignId('approved_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hrd_assessments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('branch_id');
            $table->foreignId('approved_by')->constrained('employees');
        });
    }
}
