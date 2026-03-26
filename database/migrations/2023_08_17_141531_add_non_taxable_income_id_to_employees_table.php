<?php

use App\Models\NonTaxableIncome;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNonTaxableIncomeIdToEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->foreignIdFor(NonTaxableIncome::class)->after('degree_id')->nullable()->constrained();
            $table->dropColumn('status_pernikahan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign('non_taxable_income_id_employees_foreign');
            $table->dropColumn('non_taxable_income_id');
        });
    }
}
