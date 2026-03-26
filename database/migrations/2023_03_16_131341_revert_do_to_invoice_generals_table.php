<?php

use App\Models\DeliveryOrderGeneral;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RevertDoToInvoiceGeneralsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice_generals', function (Blueprint $table) {
            $table->foreignIdFor(DeliveryOrderGeneral::class);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoice_generals', function (Blueprint $table) {
            $table->dropConstrainedForeignId('delivery_order_general_id');
        });
    }
}
