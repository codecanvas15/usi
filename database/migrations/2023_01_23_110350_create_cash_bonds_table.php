<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashBondsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cash_bonds', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Branch::class)->constrained();
            $table->foreignIdFor(\App\Models\Project::class)->nullable()->constrained();
            $table->foreignIdFor(\App\Models\Employee::class)->constrained();
            $table->foreignIdFor(\App\Models\Currency::class)->constrained();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->string('code')->unique();
            $table->date('date');
            $table->decimal('exchange_rate', 18, 3);
            $table->text('description')->nullable();
            $table->string('status')->default('pending');
            $table->text('reject_reason')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cash_bonds');
    }
}
