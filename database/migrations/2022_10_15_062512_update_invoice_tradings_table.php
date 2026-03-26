<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateInvoiceTradingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice_tradings', function (Blueprint $table) {
            $table->decimal('jumlah_diterima', 18, 4)->after('total');
            $table->decimal('jumlah_dikirim', 18, 4)->after('jumlah_diterima');
            $table->enum('calculate_from', ['sales_order', 'delivery_order'])->after('jumlah_dikirim');
            $table->decimal('lost_tolerance', 18, 4)->after('calculate_from');
            $table->enum('lost_tolerance_type', ['percent', 'liter'])->after('lost_tolerance');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoice_tradings', function (Blueprint $table) {
            $table->dropColumn([
                'lost_tolerance',
                'jumlah_diterima',
                'jumlah_dikirim',
                'calculate_from',
                'lost_tolerance_type',
            ]);
        });
    }
}
