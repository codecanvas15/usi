<?php

use App\Models\Coa;
use App\Models\ItemReceivingReport;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemReceivingReportCoasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_receiving_report_coas', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Coa::class)->constrained();
            $table->foreignIdFor(ItemReceivingReport::class)->constrained();
            $table->string('type', 60);
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
        Schema::dropIfExists('item_receiving_report_coas');
    }
}
