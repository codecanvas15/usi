<?php

use App\Models\Period;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePricesTable2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('prices', function (Blueprint $table) {
            $table->dropColumn('start_period');
            $table->dropColumn('end_period');

            $table->string('nama')->nullable()->after('item_id');
            $table->foreignIdFor(Period::class)->nullable()->constrained()->after('nama');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
