<?php

use App\Models\DeliveryOrder;
use App\Models\DeliveryOrderGeneral;
use App\Models\DeliveryOrderGeneralDetail;
use App\Models\ItemReceivingReport;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnDetail;
use App\Models\StockMutation;
use App\Models\StockOpname;
use App\Models\StockTransfer;
use App\Models\StockUsage;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDateInStockMutations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_mutations', function (Blueprint $table) {
            $table->date('date')->nullable()->after('document_code');
        });

        $stockMutations = StockMutation::all();

        foreach ($stockMutations as $stockMutation) {
            if ($stockMutation->document_model == ItemReceivingReport::class) {
                $stockMutation->date = Carbon::parse($stockMutation->document->date_receive)->format('Y-m-d');
            }

            if ($stockMutation->document_model == DeliveryOrder::class) {
                $stockMutation->date = Carbon::parse($stockMutation->document->load_date ?? $stockMutation->document->unload_date)->format('Y-m-d');
            }

            if ($stockMutation->document_model == DeliveryOrderGeneral::class) {
                $stockMutation->date = Carbon::parse($stockMutation->document->date_send ?? $stockMutation->document->date)->format('Y-m-d');
            }

            if ($stockMutation->document_model == DeliveryOrderGeneralDetail::class) {
                $stockMutation->date = Carbon::parse($stockMutation->document->delivery_order_general->date_send ?? $stockMutation->document->delivery_order_general->date)->format('Y-m-d');
            }

            if ($stockMutation->document_model == StockUsage::class) {
                $stockMutation->date = Carbon::parse($stockMutation->document->date)->format('Y-m-d');
            }

            if ($stockMutation->document_model == StockTransfer::class) {
                $stockMutation->date = Carbon::parse($stockMutation->document->date)->format('Y-m-d');
            }

            if ($stockMutation->document_model == StockOpname::class) {
                $stockMutation->date = Carbon::parse($stockMutation->document->date)->format('Y-m-d');
            }

            if ($stockMutation->document_model == PurchaseReturn::class) {
                $stockMutation->date = Carbon::parse($stockMutation->document->date)->format('Y-m-d');
            }

            if ($stockMutation->document_model == PurchaseReturnDetail::class) {
                $stockMutation->date = Carbon::parse($stockMutation->document->purchase_return->date)->format('Y-m-d');
            }

            if ($stockMutation->document_model == InvoiceReturn::class) {
                $stockMutation->date = Carbon::parse($stockMutation->document->date)->format('Y-m-d');
            }

            $stockMutation->save();
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
            $table->dropColumn(['date']);
        });
    }
}
