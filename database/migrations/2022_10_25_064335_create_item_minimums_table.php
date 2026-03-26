<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemMinimumsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_minimums', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches');
            $table->foreignId('item_id')->constrained('items');
            $table->decimal('qty', 18);
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
        Schema::dropIfExists('item_minimums');
    }
}
