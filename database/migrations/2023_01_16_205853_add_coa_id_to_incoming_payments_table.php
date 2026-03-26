<?php

use App\Models\Coa;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCoaIdToIncomingPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('incoming_payments', function (Blueprint $table) {
            $table->foreignIdFor(Coa::class)->nullable()->after('project_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('incoming_payments', function (Blueprint $table) {
            $table->dropColumn('coa_id');
        });
    }
}
