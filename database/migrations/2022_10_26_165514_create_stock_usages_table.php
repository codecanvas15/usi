<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockUsagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ware_house_id')->nullable()->constrained('ware_houses');
            $table->foreignId('branch_id')->nullable()->constrained('branches');
            $table->string('code')->nullable();
            $table->date('date')->nullable();
            $table->decimal('total', 18)->nullable();
            $table->string('note')->nullable();
            $table->string('status')->nullable();
            $table->string('created_by')->nullable();
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
        Schema::dropIfExists('stock_usages');
    }
}
