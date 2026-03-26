<?php

use App\Models\ItemReceivingReport;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReceivablesPaymentVendorLpbsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receivables_payment_vendor_lpbs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('receivables_payment_vendor_id')->nullable();
            $table->foreignIdFor(ItemReceivingReport::class)->nullable()->constrained();
            $table->decimal('outstanding', 18, 2);
            $table->decimal('amount', 18, 2);
            $table->decimal('amount_foreign', 18, 2);
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
        Schema::dropIfExists('receivables_payment_vendor_lpbs');
    }
}
