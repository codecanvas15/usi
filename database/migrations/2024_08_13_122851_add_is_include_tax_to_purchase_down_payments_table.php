<?php

use App\Models\PurchaseDownPayment;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsIncludeTaxToPurchaseDownPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_down_payments', function (Blueprint $table) {
            $table->boolean('is_include_tax')->after('code')->default(0);
            $table->decimal('subtotal', 18, 3)->after('total_amount')->nullable();
        });

        PurchaseDownPayment::all()->each(function ($purchase_down_payment) {
            $purchase_down_payment->update(['subtotal' => $purchase_down_payment->down_payment]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_down_payments', function (Blueprint $table) {
            $table->dropColumn(['is_include_tax', 'subtotal']);
        });
    }
}
