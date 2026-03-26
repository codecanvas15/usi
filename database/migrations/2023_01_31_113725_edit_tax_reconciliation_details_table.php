<?php

use App\Models\Customer;
use App\Models\Tax;
use App\Models\Vendor;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditTaxReconciliationDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tax_reconciliation_details', function (Blueprint $table) {
            $table->dropForeign('tax_reconciliation_details_journal_detail_id_foreign');
            $table->dropColumn('journal_detail_id');
            $table->dropForeign('tax_reconciliation_details_tax_reconciliation_balance_id_foreign');
            $table->dropColumn('tax_reconciliation_balance_id');

            $table->string('reference_model')->after('tax_reconciliation_id');
            $table->bigInteger('reference_id')->after('reference_model');
            $table->string('reference_parent_model')->after('reference_id');
            $table->bigInteger('reference_parent_id')->after('reference_parent_model');
            $table->foreignIdFor(Vendor::class)->after('reference_parent_id')->nullable();
            $table->foreignIdFor(Customer::class)->after('vendor_id')->nullable();
            $table->foreignIdFor(Tax::class)->after('customer_id');
            $table->decimal('dpp', 18, 3)->after('tax_id');
            $table->decimal('value', 18, 3)->after('dpp');
            $table->decimal('amount', 18, 3)->after('value');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tax_reconciliation_details', function (Blueprint $table) {
            $table->dropForeign('tax_reconciliation_details_tax_id_foreign');
            $table->decimal('dpp', 18, 3)->after('tax_id');
            $table->decimal('value', 18, 3)->after('dpp');
            $table->decimal('amount', 18, 3)->after('value');
        });
    }
}
