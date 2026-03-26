<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockOpnamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_opnames', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ware_house_id')->nullable()->constrained('ware_houses');
            $table->foreignId('branch_id')->nullable()->constrained('branches');
            $table->string('code')->nullable();
            $table->date('date')->nullable();
            $table->string('created_by')->nullable();
            $table->decimal('less_difference', 18)->nullable();
            $table->decimal('more_difference', 18)->nullable();
            $table->string('status')->nullable();
            $table->string('owner_status')->nullable();
            $table->string('manager_marketing_status')->nullable();
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
        Schema::dropIfExists('stock_opnames');
    }
}
