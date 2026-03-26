<?php

use App\Models\EmploymentStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEmployeeTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            // drop
            $table->dropColumn(['nomor_rekening', 'posisi', 'employement_status']);
            $table->dropConstrainedForeignId('user_id');
            $table->renameColumn('foto_sim', 'foto_id');

            // Add new columns
            $table->foreignIdFor(EmploymentStatus::class)->nullable()->after('division_id')->constrained();
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
