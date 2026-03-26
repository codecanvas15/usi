<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('prices', function (Blueprint $table) {
            // $table->dropColumn('harga');

            $table->date('start_period')->nullable()->after('item_id');
            $table->date('end_period')->nullable()->after('start_period');
            $table->decimal('harga_beli', 18, 2)->default(0)->after('end_period');
            $table->decimal('harga_jual', 18, 2)->default(0)->after('harga_beli');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('prices', function (Blueprint $table) {
            $table->dropColumn('tahun');
            $table->dropColumn('periode');
            $table->dropColumn('harga_beli');
            $table->dropColumn('harga_jual');

            // $table->integer('harga')->default(0)->after('item_id');
        });
    }
}
