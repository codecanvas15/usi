<?php

use App\Models\Branch;
use App\Models\DeliveryOrder;
use App\Models\Fleet;
use App\Models\ItemReceivingReport;
use App\Models\PurchaseTransport;
use App\Models\PurchaseTransportDetail;
use App\Models\ShNumber;
use App\Models\SoTrading;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropDeliveryOrderAndRecreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('delivery_order_types');
        Schema::dropIfExists('delivery_order_details');

        Schema::table('invoice_trading_details', function (Blueprint $table) {
            $table->dropConstrainedForeignId('delivery_order_id');
        });

        Schema::dropIfExists('delivery_orders');

        Schema::create('delivery_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(SoTrading::class)->nullable()->constrained('sale_orders');
            $table->foreignIdFor(PurchaseTransport::class)->nullable()->constrained();
            $table->foreignIdFor(PurchaseTransportDetail::class)->nullable()->constrained();
            $table->foreignIdFor(Fleet::class)->nullable()->nullable()->constrained();
            $table->foreignIdFor(Branch::class)->nullable()->nullable()->constrained();
            $table->foreignIdFor(ShNumber::class)->nullable()->nullable()->constrained();
            $table->foreignIdFor(ItemReceivingReport::class)->nullable()->nullable()->constrained();
            $table->string('code')->unique();
            $table->date('target_delivery')->nullable();
            $table->date('load_date')->nullable();
            $table->date('unload_date')->nullable();
            $table->decimal('load_quantity_realization', 18, 3)->nullable();
            $table->decimal('load_quantity', 18, 3)->nullable();
            $table->decimal('unload_quantity', 18, 3)->nullable();
            $table->decimal('unload_quantity_realization', 18, 3)->nullable();
            $table->string('file')->nullable();
            $table->string('description')->nullable();
            $table->string('top_seal', 100)->nullable();
            $table->string('bottom_seal', 100)->nullable();
            $table->string('temperature', 100)->nullable();
            $table->string('initial_meter', 100)->nullable();
            $table->string('initial_final', 100)->nullable();
            $table->string('sg_meter', 100)->nullable();
            $table->string('status', 60);
            $table->boolean('is_invoice_created')->default(false);
            $table->boolean('is_item_receiving_report_created')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('invoice_trading_details', function (Blueprint $table) {
            $table->foreignIdFor(DeliveryOrder::class)->nullable()->after('invoice_trading_id')->constrained();
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
