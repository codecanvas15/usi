<?php

use App\Models\Branch;
use App\Models\Division;
use App\Models\Employee;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractExtensionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contract_extensions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Branch::class)->constrained()->nullable();
            $table->foreignIdFor(Employee::class)->constrained()->nullable();
            $table->foreignIdFor(Division::class)->constrained()->nullable();
            $table->date('from_date')->nullable();
            $table->date('to_date')->nullable();
            $table->string('code')->nullable();
            $table->string('submission_status')->nullable();
            $table->string('status')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
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
        Schema::dropIfExists('contract_extensions');
    }
}
