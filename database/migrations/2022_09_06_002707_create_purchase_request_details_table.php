<?php

use App\Models\PurchaseRequest;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseRequestDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_request_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(PurchaseRequest::class)->constrained();
            $table->string('item');
            $table->decimal('jumlah', 18, 2);
            $table->string('status', 24);
            $table->string('keterangan')->nullable();
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
        Schema::dropIfExists('purchase_request_details');
    }
}
