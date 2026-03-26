<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashBondReturnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cash_bond_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Branch::class)->constrained();
            $table->foreignIdFor(\App\Models\Project::class)->nullable()->constrained();
            $table->foreignIdFor(\App\Models\Employee::class)->constrained();
            $table->foreignIdFor(\App\Models\Currency::class)->nullable()->constrained();
            $table->foreignIdFor(\App\Models\Coa::class)->constrained();
            $table->string('code');
            $table->date('date');
            $table->string('description')->nullable();
            $table->decimal('exchange_rate', 18, 3)->default(1);
            $table->string('status')->default('pending');
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
        Schema::dropIfExists('cash_bond_returns');
    }
}
