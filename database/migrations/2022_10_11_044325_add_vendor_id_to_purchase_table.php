<?php

use App\Models\Vendor;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVendorIdToPurchaseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_generals', function (Blueprint $table) {
            $table->foreignIdFor(Vendor::class)->after('currency_id')->constrained();
        });

        Schema::table('purchase_order_services', function (Blueprint $table) {
            $table->foreignIdFor(Vendor::class)->after('currency_id')->constrained();
        });

        Schema::table('purchase_transports', function (Blueprint $table) {
            $table->foreignIdFor(Vendor::class)->after('ware_house_id')->constrained();
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->foreignIdFor(Vendor::class)->after('currency_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_order_general', function (Blueprint $table) {
            //
        });
    }
}
