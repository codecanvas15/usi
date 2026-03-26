<?php

use App\Models\ItemReceivingReport;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddItemReceivingReportIdToSupplierInvoicePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('supplier_invoice_payments', function (Blueprint $table) {
            $table->foreignIdFor(ItemReceivingReport::class)->nullable()->after('supplier_invoice_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('supplier_invoice_payments', function (Blueprint $table) {
            $table->dropForeign(['item_receiving_report_id']);
        });
    }
}
