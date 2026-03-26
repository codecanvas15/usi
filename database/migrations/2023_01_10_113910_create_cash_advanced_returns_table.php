<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashAdvancedReturnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cash_advanced_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Branch::class)->constrained();
            $table->foreignIdFor(\App\Models\Project::class)->nullable()->constrained();
            $table->foreignIdFor(\App\Models\Currency::class)->constrained();
            $table->unsignedBigInteger('reference_id');
            $table->string('reference_model');
            $table->string('type', 30);
            $table->date('date');
            $table->string('code', 60);
            $table->string('status', 60);
            $table->decimal('exchange_rate', 18, 3)->default(1);
            $table->decimal('amount_total', 18, 3)->default(0);
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
        Schema::dropIfExists('cash_advanced_returns');
    }
}
