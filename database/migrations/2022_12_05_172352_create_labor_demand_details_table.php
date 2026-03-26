<?php

use App\Models\LaborDemand;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaborDemandDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('labor_demand_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(LaborDemand::class)->constrained();
            $table->string('degree');
            $table->string('major');
            $table->string('age');
            $table->string('gender');
            $table->boolean('work_exp')->default(false);
            $table->string('work_exp_field')->nullable();
            $table->string('work_exp_years')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('labor_demand_details');
    }
}
