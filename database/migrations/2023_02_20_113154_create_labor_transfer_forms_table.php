<?php

use App\Models\Branch;
use App\Models\Employee;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaborTransferFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('labor_transfer_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained();
            $table->foreignIdFor(Branch::class)->constrained();
            $table->string('reference');
            $table->string('from_company');
            $table->foreignId('from_branch')->constrained('branches');
            $table->foreignId('from_division')->constrained('divisions');
            $table->string('to_company');
            $table->foreignId('to_branch')->constrained('branches');
            $table->foreignId('to_division')->constrained('divisions');
            $table->mediumText('reason');
            $table->foreignId('submitted_by')->constrained('employees');
            $table->foreignId('created_by')->constrained('employees');
            $table->foreignId('approved_by')->nullable()->constrained('employees');
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
        Schema::dropIfExists('labor_transfer_forms');
    }
}
