<?php

use App\Models\Employee;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmployeeIdToVehicleFleetTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vehicle_fleets', function (Blueprint $table) {
            $table->foreignIdFor(Employee::class)->nullable()->after('fleet_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vehicle_fleets', function (Blueprint $table) {
            $table->dropConstrainedForeignId('employee_id');
        });
    }
}
