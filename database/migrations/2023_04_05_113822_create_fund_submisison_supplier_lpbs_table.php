<?php

use App\Models\ItemReceivingReport;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFundSubmisisonSupplierLpbsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fund_submisison_supplier_lpbs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('fund_submission_supplier_detail_id');
            $table->foreignIdFor(ItemReceivingReport::class)->constrained();
            $table->decimal('outstanding', 20, 2);
            $table->decimal('amount', 20, 2);
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
        Schema::dropIfExists('fund_submisison_supplier_lpbs');
    }
}
