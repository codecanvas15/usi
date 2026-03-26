<?php

use App\Models\ItemReceivingReport;
use App\Models\Purchase;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddPurchaseIdToItemReceivingReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_receiving_reports', function (Blueprint $table) {
            $table->foreignIdFor(Purchase::class)->nullable()->after('branch_id')->constrained('purchases');
        });

        $lpbs = ItemReceivingReport::withTrashed()->get();

        foreach ($lpbs as $key => $value) {
            ItemReceivingReport::withTrashed()
                ->where('id', $value->id)
                ->update(['purchase_id' => $value->reference->purchase_id]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item_receiving_reports', function (Blueprint $table) {
            $table->dropForeign('item_receiving_reports_purchase_id_foreign');
            $table->dropColumn('purchase_id');
        });
    }
}
