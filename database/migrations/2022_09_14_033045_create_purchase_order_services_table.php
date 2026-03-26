<?php

use App\Models\Purchase;
use App\Models\PurchaseRequest;
use App\Models\Supplier;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseOrderServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_order_services', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 24);
            $table->foreignIdFor(Purchase::class)->constrained();
            $table->foreignIdFor(PurchaseRequest::class)->nullable()->constrained();
            $table->foreignIdFor(Supplier::class)->constrained();
            $table->string('status', 24);
            $table->decimal('ppn', 18, 2);
            $table->decimal('sub_total', 18, 2)->nullable();
            $table->decimal('total', 18, 2)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_order_services');
    }
}
