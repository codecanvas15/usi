<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddProjectToPurchaseRequest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('cascade');
        });

        $purchaseRequest = \App\Models\PurchaseRequest::all();

        $purchaseOrderGeneralDetailItems = DB::table('purchase_order_general_detail_items')
            ->leftJoin('purchase_order_general_details', 'purchase_order_general_details.id', 'purchase_order_general_detail_items.purchase_order_general_detail_id')
            ->leftJoin('purchase_order_generals', 'purchase_order_generals.id', 'purchase_order_general_details.purchase_order_general_id')
            ->whereNull('purchase_order_generals.deleted_at')
            ->whereNotIn('purchase_order_generals.status', ['void'])
            ->selectRaw('purchase_order_general_details.purchase_request_id as purchase_request_id, purchase_order_generals.project_id as project_id')
            ->get();

        $purchaseOrderServiceDetailItems = DB::table('purchase_order_service_detail_items')
            ->leftJoin('purchase_order_service_details', 'purchase_order_service_details.id', 'purchase_order_service_detail_items.purchase_order_service_detail_id')
            ->leftJoin('purchase_order_services', 'purchase_order_services.id', 'purchase_order_service_details.purchase_order_service_id')
            ->whereNull('purchase_order_services.deleted_at')
            ->whereNotIn('purchase_order_services.status', ['void'])
            ->selectRaw('purchase_order_service_details.purchase_request_id as purchase_request_id, purchase_order_services.project_id as project_id')
            ->get();

        try {
            foreach ($purchaseRequest as $key => $value) {
                if ($value->type == 'jasa') {
                    $purchaseOrderServiceDetailItems = collect($purchaseOrderServiceDetailItems)->where('purchase_request_id', $value->id)->first();
                    if ($purchaseOrderServiceDetailItems) {
                        $value->project_id = $purchaseOrderServiceDetailItems->project_id;
                        $value->save();
                    }
                } else {
                    $purchaseOrderGeneralDetailItems = collect($purchaseOrderGeneralDetailItems)->where('purchase_request_id', $value->id)->first();
                    if ($purchaseOrderGeneralDetailItems) {
                        $value->project_id = $purchaseOrderGeneralDetailItems->project_id;
                        $value->save();
                    }
                }
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropColumn('project_id');
        });
    }
}
