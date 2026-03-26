<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddCategoryToTaxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('taxes', function (Blueprint $table) {
            $table->string('category')->nullable();
        });

        $taxes = DB::table('taxes')->get();
        foreach ($taxes as $key => $tax) {
            DB::table('taxes')
                ->where('id', $tax->id)
                ->update(['category' => $tax->name]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('taxes', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
}
