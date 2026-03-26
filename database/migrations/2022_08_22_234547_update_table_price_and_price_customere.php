<?php

use App\Models\Price;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTablePriceAndPriceCustomere extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('price_customers', function (Blueprint $table) {
            $table->foreignIdFor(Price::class)->after('sh_number_id')->constrained();
            $table->softDeletes()->after('price_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('price_customers', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
}
