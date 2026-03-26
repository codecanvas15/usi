<?php

use App\Models\Tax;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTaxIdToPurchaseOrderGeneralDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_general_details', function (Blueprint $table) {
            $table->foreignIdFor(Tax::class)->nullable()->after('harga')->constrained();
            $table->decimal('value_tax', 18, 4)->nullable()->after('tax_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_order_general_details', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tax_id');
            $table->dropColumn('value_tax');
        });
    }
}
