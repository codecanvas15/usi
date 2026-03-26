<?php

use App\Models\Branch;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSupplierInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('supplier_invoices', function (Blueprint $table) {
            $table->dropColumn('tax_reference');
            $table->dropColumn('item_receiving_report_id');
            $table->renameColumn('tax', 'tax_total');
            $table->renameColumn('total', 'grand_total');
            $table->string('reference')->unique()->change();
            $table->foreignIdFor(Branch::class)->nullable()->after('id')->constrained();
            $table->enum('term_of_payment', ['cash', 'by days'])->default('cash')->after('date');
            $table->integer('top_days')->nullable()->after('term_of_payment');
            $table->date('top_due_date')->nullable()->after('top_days');
            $table->foreignId('approved_by')->nullable()->after('status')->constrained('users');
            $table->enum('approval_status', ['pending','approve','reject'])->default('pending')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('supplier_invoices', function (Blueprint $table) {
            //
        });
    }
}
