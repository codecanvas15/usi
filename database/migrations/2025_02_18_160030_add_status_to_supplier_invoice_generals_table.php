<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddStatusToSupplierInvoiceGeneralsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $supplier_invoice_generals = DB::table('supplier_invoice_generals')->get();
        Schema::table('supplier_invoice_generals', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('supplier_invoice_generals', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approve', 'rejected', 'revert', 'void'])->default('pending');
        });

        foreach ($supplier_invoice_generals as $supplier_invoice_general) {
            DB::table('supplier_invoice_generals')
                ->where('id', $supplier_invoice_general->id)
                ->update([
                    'status' => $supplier_invoice_general->status !== '' ? $supplier_invoice_general->status : 'void',
                ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('supplier_invoice_generals', function (Blueprint $table) {});
    }
}
