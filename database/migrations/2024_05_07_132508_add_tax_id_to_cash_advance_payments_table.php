<?php

use App\Models\Tax;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTaxIdToCashAdvancePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cash_advance_payments', function (Blueprint $table) {
            $table->foreignIdFor(Tax::class)->after('currency_id')->nullable();
            $table->string('tax_number')->after('tax_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cash_advance_payments', function (Blueprint $table) {
            $table->dropForeign('cash_advance_payments_tax_id_foreign');
            $table->dropColumn(['tax_id', 'tax_number']);
        });
    }
}
