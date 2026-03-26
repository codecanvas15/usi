<?php

use App\Models\Coa;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCodeToReceivablesPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('receivables_payments', function (Blueprint $table) {
            $table->foreignIdFor(Coa::class)->after('customer_id')->nullable()->constrained();
            $table->string('code')->after('exchange_rate');
            $table->decimal('total', 18, 3)->after('reference');
            $table->decimal('exchange_rate_gap_total', 18, 3)->after('total');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('receivables_payments', function (Blueprint $table) {
            $table->dropColumn('coa_id');
            $table->dropColumn('code');
            $table->dropColumn('total');
            $table->dropColumn('exchange_rate_gap_total');
        });
    }
}
