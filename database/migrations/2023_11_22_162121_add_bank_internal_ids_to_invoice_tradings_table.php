<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddBankInternalIdsToInvoiceTradingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice_tradings', function (Blueprint $table) {
            $table->text('bank_internal_ids')->default('');
        });

        $invoice_tradings = \App\Models\InvoiceTrading::all();
        foreach ($invoice_tradings as $key => $invoice_trading) {
            DB::table('invoice_tradings')
                ->where('id', $invoice_trading->id)
                ->update([
                    'bank_internal_ids' => '[' . $invoice_trading->bank_internal_id . ']'
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
        Schema::table('invoice_tradings', function (Blueprint $table) {
            $table->dropColumn('bank_internal_ids');
        });
    }
}
