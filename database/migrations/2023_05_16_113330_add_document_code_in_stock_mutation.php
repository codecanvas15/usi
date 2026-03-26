<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDocumentCodeInStockMutation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_mutations', function (Blueprint $table) {
            $table->string('document_code')->nullable()->after('document_id');
        });

        $stock_mutations = \App\Models\StockMutation::all();
        foreach ($stock_mutations as $stock_mutation) {
            $stock_mutation->document_code = $stock_mutation->document->code
                ?? $stock_mutation->document->kode
                ?? $stock_mutation->document->no
                ?? $stock_mutation->document->delivery_order_general?->code
                ?? $stock_mutation->document->purchase_return?->code
                ?? null;
            $stock_mutation->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_mutations', function (Blueprint $table) {
            $table->dropColumn('document_code');
        });
    }
}
