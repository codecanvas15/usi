<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateParentInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice_tradings', function (Blueprint $table) {
            $table->dropColumn([
                'ppn',
                'ppn_amount',
                'other_cost_after_ppn',
            ]);

            $table->decimal('subtotal_after_tax', 18, 3)->nullable()->after('subtotal');

            $table->decimal('total_jumlah_diterima', 18, 3)->nullable()->change();
            $table->decimal('total', 18, 3)->nullable()->change();
            $table->decimal('calculate_from', 18, 3)->nullable()->change();
            $table->decimal('lost_tolerance', 18, 3)->nullable()->change();
            $table->decimal('lost_tolerance_type', 18, 3)->nullable()->change();
            $table->decimal('tolerance_amount', 18, 3)->nullable()->change();
            $table->decimal('total_lost', 18, 3)->nullable()->change();
            $table->decimal('total_jumlah_dikirim', 18, 3)->nullable()->change();
            $table->decimal('jumlah', 18, 3)->nullable()->change();
            $table->decimal('harga', 18, 3)->nullable()->change();
            $table->decimal('subtotal', 18, 3)->nullable()->change();
            $table->decimal('additional_tax_total', 18, 3)->nullable()->change();
            $table->decimal('after_additional_tax', 18, 3)->nullable()->change();
            $table->decimal('other_cost', 18, 3)->nullable()->change();
            $table->decimal('total_other_cost', 18, 3)->nullable()->change();
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
