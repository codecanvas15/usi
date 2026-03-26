<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyInvoiceTradingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice_tradings', function (Blueprint $table) {
            $table->dropColumn('sub_total');
            $table->dropColumn('jumlah_diterima');
            $table->dropColumn('jumlah_dikirim');
            $table->unsignedBigInteger('customer_id')->after('branch_id');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->unsignedBigInteger('item_id')->after('customer_id');
            $table->foreign('item_id')->references('id')->on('items');
            $table->date('date')->after('item_id');
            $table->string('nomor_po_external', 50)->after('kode');
            $table->decimal('total_jumlah_diterima', 18, 3)->after('nomor_po_external');
            $table->decimal('tolerance_amount', 18, 3)->after('lost_tolerance_type');
            $table->decimal('total_lost', 18, 3)->after('tolerance_amount');
            $table->decimal('total_jumlah_dikirim', 18, 3)->after('total_lost');
            $table->decimal('jumlah', 18, 3)->after('total_jumlah_dikirim');
            $table->decimal('harga', 18, 3)->after('jumlah');
            $table->decimal('subtotal', 18, 3)->after('harga');
            $table->decimal('additional_tax_total', 18, 3)->after('subtotal');
            $table->decimal('after_additional_tax', 18, 3)->after('additional_tax_total');
            $table->decimal('other_cost', 18, 3)->after('after_additional_tax');
            $table->decimal('total_other_cost', 18, 3)->after('other_cost');
            $table->decimal('ppn', 18, 3)->after('total_other_cost');
            $table->decimal('ppn_amount', 18, 3)->after('ppn');
            $table->decimal('other_cost_after_ppn', 18, 3)->after('ppn_amount');
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
            $table->dropColumn('date');
            $table->dropColumn('nomor_po_external');
            $table->dropColumn('total_jumlah_diterim');
            $table->dropColumn('tolerance_amount');
            $table->dropColumn('total_lost');
            $table->dropColumn('total_jumlah_dikirim');
            $table->dropColumn('jumlah');
            $table->dropColumn('harga');
            $table->dropColumn('subtotal');
            $table->dropColumn('additional_tax_total');
            $table->dropColumn('after_additional_tax');
            $table->dropColumn('other_cost');
            $table->dropColumn('total_other_cost');
            $table->dropColumn('ppn');
            $table->dropColumn('ppn_amount');
            $table->dropColumn('other_cost_after_ppn');
        });
    }
}
