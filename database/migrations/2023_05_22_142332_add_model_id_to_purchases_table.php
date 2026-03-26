<?php

use App\Models\Currency;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddModelIdToPurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->bigInteger('model_id')->after('model_reference')->nullable();
            $table->foreignIdFor(Currency::class)->nullable()->constrained()->after('vendor_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropForeign(['currency_id']);
            $table->dropColumn('currency_id');
            $table->dropColumn('model_id');
        });
    }
}
