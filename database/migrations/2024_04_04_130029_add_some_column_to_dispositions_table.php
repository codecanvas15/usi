<?php

use App\Models\BankInternal;
use App\Models\Customer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSomeColumnToDispositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dispositions', function (Blueprint $table) {
            $table->foreignIdFor(Customer::class)->after('tax_id')->nullable();
            $table->foreignIdFor(BankInternal::class)->after('customer_id')->nullable();
            $table->integer('due')->nullable()->after('date');
            $table->date('due_date')->nullable()->after('due');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dispositions', function (Blueprint $table) {
            $table->dropForeign('dispositions_customer_id_foreign');
            $table->dropForeign('dispositions_bank_internal_id_foreign');
            $table->dropColumn(['customer_id', 'bank_internal_id', 'due', 'due_date']);
        });
    }
}
