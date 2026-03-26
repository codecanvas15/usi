<?php

use App\Models\ItemReceivingReport;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemReceivingPoTradingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_receiving_po_tradings', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ItemReceivingReport::class)->constrained();
            $table->decimal('liter_15', 18, 2);
            $table->decimal('liter_obs', 18, 2);
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
        Schema::dropIfExists('item_receiving_po_tradings');
    }
}
