<?php

use App\Models\PoTrading;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeShNumberIdToPurchaseOrderIdInPurchaseTransportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_transports', function (Blueprint $table) {
            $table->dropConstrainedForeignId('sh_number_id');
            $table->foreignIdFor(PoTrading::class)->after('so_trading_id')->constrained('purchase_orders');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_transports', function (Blueprint $table) {
            //
        });
    }
}
