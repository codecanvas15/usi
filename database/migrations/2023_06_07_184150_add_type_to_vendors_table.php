<?php

use App\Models\PoTrading;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeToVendorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->string('type', 30)->nullable()->after('nama');
        });

        $tradings = PoTrading::all();
        $tradingVendorIds = $tradings->pluck('vendor_id')->toArray();

        \App\Models\Vendor::whereIn('id', $tradingVendorIds)->update([
            'type' => 'trading'
        ]);

        \App\Models\Vendor::whereNotIn('id', $tradingVendorIds)->update([
            'type' => 'general'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vendors', function (Blueprint $table) {
            //
        });
    }
}
