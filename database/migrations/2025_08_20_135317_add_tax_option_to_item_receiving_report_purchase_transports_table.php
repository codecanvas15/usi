<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddTaxOptionToItemReceivingReportPurchaseTransportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_receiving_report_purchase_transports', function (Blueprint $table) {
            $table->enum('tax_option', ['normal', 'full', 'by_po'])->default('normal');
            $table->double('subtotal_by_po')->after('sub_total');
        });

        DB::table('item_receiving_report_purchase_transports')
            ->where('is_tax_full', 1)
            ->update([
                'tax_option' => 'full'
            ]);

        Schema::table('item_receiving_report_purchase_transports', function (Blueprint $table) {
            $table->dropColumn('is_tax_full');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item_receiving_report_purchase_transports', function (Blueprint $table) {
            $table->dropColumn('subtotal_by_po');
            $table->boolean('is_tax_full')->default(0);
            $table->dropColumn('tax_option');
        });
    }
}
