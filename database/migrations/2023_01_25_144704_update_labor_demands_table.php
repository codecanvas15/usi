<?php

use App\Models\LaborDemand;
use App\Models\Position;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateLaborDemandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('labor_demand_details');
        Schema::dropIfExists('labor_demands');

        Schema::create('labor_demands', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Branch::class)->constrained();
            $table->foreignIdFor(\App\Models\Division::class)->constrained();
            $table->foreignIdFor(\App\Models\User::class)->constrained();
            $table->string('code');
            $table->string('location');
            $table->string('status');
            $table->foreignId('approved_by_hrd')->nullable()->constrained('users');
            $table->foreignId('approved_by_director')->nullable()->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('labor_demand_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\LaborDemand::class)->constrained();
            $table->foreignIdFor(\App\Models\Position::class)->constrained();

            $table->string('position_name');

            $table->string('degree');
            $table->string('major')->nullable();
            $table->string('gender');

            $table->integer('age');
            $table->integer('quantity');
            $table->integer('long_work_experience')->nullable();

            $table->text('work_experience')->nullable();
            $table->text('skills')->nullable();
            $table->text('job_description')->nullable();
            $table->text('description')->nullable();
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
        Schema::dropIfExists('labor_demands');

        Schema::create('labor_demands', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Position::class)->constrained();
            $table->string('position_name');
            $table->foreignId('request_by')->nullable()->constrained('employees');
            $table->string('reference');
            $table->foreignId('approved_by_hrd')->nullable()->constrained('employees');
            $table->foreignId('approved_by_director')->nullable()->constrained('employees');
            $table->enum('status', ['pending hrd approval', 'pending director approval', 'approved', 'rejected'])->default('pending hrd approval');
            $table->timestamps();
        });

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
}
