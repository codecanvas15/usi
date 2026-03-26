<?php

use App\Models\Coa;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCoaIdToFundSubmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fund_submissions', function (Blueprint $table) {
            $table->foreignIdFor(Coa::class)->after('branch_id')->nullable()->constrained();
            $table->integer('exchange_rate')->nullable()->after('currency_id');
            $table->dropColumn('vendor');
            $table->dropColumn('alamat');
            $table->string('to_model')->after('coa_id')->nullable();
            $table->bigInteger('to_id')->after('to_model')->nullable();
            $table->string('to_name')->after('to_id')->nullable();
            $table->date('date')->after('to_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fund_submissions', function (Blueprint $table) {
            $table->dropColumn('coa_id');
            $table->dropColumn('exchange_rate');
            $table->dropColumn('to_model');
            $table->dropColumn('to_id');
        });
    }
}
