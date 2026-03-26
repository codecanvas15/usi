<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaborApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('labor_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Branch::class)->constrained();
            $table->foreignIdFor(\App\Models\Employee::class)->nullable()->constrained();
            $table->foreignIdFor(\App\Models\LaborDemandDetail::class)->constrained();
            $table->string('code', 60);
            $table->date('date');
            $table->string('name');
            $table->string('email');
            $table->string('address');
            $table->string('address_domicil');
            $table->string('phone', 24);
            $table->date('date_of_birth');
            $table->string('place_of_birth');
            $table->string('religion');
            $table->string('gender', 30);
            $table->boolean('marital_status');
            $table->string('identity_card_number', 60);
            $table->string('status', 30);
            $table->softDeletes();
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
        Schema::dropIfExists('labor_applications');
    }
}
