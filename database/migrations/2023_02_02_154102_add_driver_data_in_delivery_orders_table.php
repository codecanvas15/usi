<?php

use App\Models\Employee;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDriverDataInDeliveryOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('delivery_orders', function (Blueprint $table) {
            $table->foreignIdFor(Employee::class)->nullable()->after('fleet_id')->constrained();
            $table->string('driver_name')->nullable()->after('is_item_receiving_report_created');
            $table->string('driver_phone')->nullable()->after('driver_name', 24);
            $table->string('vehicle_information')->nullable()->after('driver_phone');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('delivery_orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('employee_id');
            $table->dropColumn(['driver_name', 'driver_phone']);
        });
    }
}
