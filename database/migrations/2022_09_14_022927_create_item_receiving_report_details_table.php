<?php

use App\Models\ItemReceivingReport;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemReceivingReportDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_receiving_report_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ItemReceivingReport::class)->constrained();
            $table->decimal('jumlah_diterima', 18, 2);
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
        Schema::dropIfExists('item_receiving_report_details');
    }
}
