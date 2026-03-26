<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJumlahDiterimaInPurchaseOrderGeneralDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_general_details', function (Blueprint $table) {
            $table->decimal('jumlah_diterima', 18, 2)->default(0)->nullable()->after('jumlah');
            $table->string('status', 60)->nullable()->after('jumlah_diterima');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_order_general_details', function (Blueprint $table) {
            $table->dropColumn('jumlah_diterima');
            $table->dropColumn('status');
        });
    }
}
