<?php

use App\Models\Employee;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGpEvaluationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gp_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained();
            $table->foreignId('created_by')->nullable()->constrained('employees');
            $table->foreignId('approved_by')->nullable()->constrained('employees');
            $table->string('reference');
            $table->date('date');
            $table->integer('total_score')->default(0);
            $table->mediumText('notes')->nullable();
            $table->enum('approval_status', ['pending','approve','reject'])->default('pending');
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
        Schema::dropIfExists('gp_evaluations');
    }
}
