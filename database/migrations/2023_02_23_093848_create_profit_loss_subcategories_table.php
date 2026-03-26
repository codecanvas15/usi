<?php

use App\Models\ProfitLossCategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfitLossSubcategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profit_loss_subcategories', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ProfitLossCategory::class)->constrained();
            $table->string('name');
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
        Schema::dropIfExists('profit_loss_subcategories');
    }
}
