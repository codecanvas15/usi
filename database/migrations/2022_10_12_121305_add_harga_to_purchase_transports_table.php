<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHargaToPurchaseTransportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_transports', function (Blueprint $table) {
            $table->decimal('harga', 18, 2)->after('status');
            $table->decimal('total', 18, 2)->after('harga');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_transports', function (Blueprint $table) {
            $table->dropColumn('harga');
        });
    }
}
