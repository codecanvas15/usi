<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddOrderToProfitLossSubcategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('profit_loss_subcategories', function (Blueprint $table) {
            $table->integer('order')->after('type')->default(0);
        });

        $data = [
            'pendapatan' => 1,
            'harga-pokok-penjualan' => 2,
            'biaya-operasional' => 3,
            'biaya-diluar-usaha' => 5,
            'pendapatan-diluar-usaha' => 4
        ];

        foreach ($data as $key => $value) {
            DB::table('profit_loss_subcategories')
                ->where('name', $key)
                ->update(['order' => $value]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('profit_loss_subcategories', function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }
}
