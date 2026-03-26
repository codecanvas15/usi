<?php

use App\Models\Currency;
use App\Models\Vendor;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCurrencyVendorToItemReceivingReport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_receiving_reports', function (Blueprint $table) {
            $table->foreignIdFor(Currency::class)->nullable()->constrained()->after('price_id');
            $table->foreignIdFor(Vendor::class)->nullable()->constrained()->after('currency_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item_receiving_reports', function (Blueprint $table) {
            //
        });
    }
}
