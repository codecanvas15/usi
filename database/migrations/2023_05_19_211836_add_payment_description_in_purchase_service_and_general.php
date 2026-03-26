<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentDescriptionInPurchaseServiceAndGeneral extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_generals', function (Blueprint $table) {
            $table->string('payment_description')->nullable()->after('term_of_payment_days');
        });

        Schema::table('purchase_order_services', function (Blueprint $table) {
            $table->string('payment_description')->nullable()->after('term_of_payment_days');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_order_generals', function (Blueprint $table) {
            $table->dropColumn(['payment_description']);
        });

        Schema::table('purchase_order_services', function (Blueprint $table) {
            $table->dropColumn(['payment_description']);
        });
    }
}
