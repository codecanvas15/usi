<?php

use App\Models\Position;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaborDemandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('labor_demands', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Position::class)->constrained();
            $table->string('position_name');
            $table->foreignId('request_by')->constrained('employees');
            $table->string('reference');
            $table->foreignId('approved_by_hrd')->nullable()->constrained('employees');
            $table->foreignId('approved_by_director')->nullable()->constrained('employees');
            $table->enum('status', ['pending hrd approval','pending director approval','approved','rejected'])->default('pending hrd approval');
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
        Schema::dropIfExists('labor_demands');
    }
}
