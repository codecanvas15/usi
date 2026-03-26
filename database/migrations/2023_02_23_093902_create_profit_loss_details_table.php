<?php

use App\Models\Coa;
use App\Models\ProfitLossSubcategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfitLossDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profit_loss_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ProfitLossSubcategory::class);
            $table->foreignIdFor(Coa::class);
            $table->integer('position')->default(0);
            $table->string('type');
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
        Schema::dropIfExists('profit_loss_details');
    }
}
