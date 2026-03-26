<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTaxTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('taxes', function (Blueprint $table) {
            $table->renameColumn('nama', 'name');
            $table->decimal('value', 18, 18)->change();
            $table->string('type', 60)->after('nama');
            $table->string('description')->nullable()->after('type');
            $table->foreignId('coa_sale')->constrained('coas')->after('description');
            $table->foreignId('coa_purchase')->constrained('coas')->after('coa_sale');
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
