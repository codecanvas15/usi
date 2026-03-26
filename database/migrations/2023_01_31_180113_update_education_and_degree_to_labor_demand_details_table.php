<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEducationAndDegreeToLaborDemandDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('labor_demand_details', function (Blueprint $table) {
            $table->dropColumn('degree');
            $table->foreignId('education_id')->nullable()->constrained('educations');
            $table->foreignIdFor(\App\Models\Degree::class)->nullable()->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('labor_demand_details', function (Blueprint $table) {
            $table->string('degree');
            $table->dropForeign(['education_id', 'degree_id']);
        });
    }
}
