<?php

use App\Models\ModelTable;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;

class AddTaxAttachmentToCashAdvancePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fund_submissions', function (Blueprint $table) {
            $table->text('tax_attachment')->after('tax_number')->nullable();
        });

        Schema::table('cash_advance_payments', function (Blueprint $table) {
            $table->text('tax_attachment')->after('tax_number')->nullable();
        });

        Schema::table('cash_advance_receives', function (Blueprint $table) {
            $table->text('tax_attachment')->after('tax_number')->nullable();
        });

        ModelTable::insert([
            [
                'name' => 'App\\Models\\InvoiceDownPayment',
                'alias' => 'invoice-down-payment',
                'type' => null,
                'group' => 'penjualan',
                'need_to_check_amount' => 0,
            ],
            [
                'name' => 'App\\Models\\PurchaseDownPayment',
                'alias' => 'purchase-down-payment',
                'type' => null,
                'group' => 'pembelian',
                'need_to_check_amount' => 0,
            ]
        ]);

        Permission::insert([
            [
                'name' => 'view invoice-down-payment',
                'group' => 'invoice-down-payment',
                'guard_name' => 'web',
            ],
            [
                'name' => 'create invoice-down-payment',
                'group' => 'invoice-down-payment',
                'guard_name' => 'web',
            ],
            [
                'name' => 'edit invoice-down-payment',
                'group' => 'invoice-down-payment',
                'guard_name' => 'web',
            ],
            [
                'name' => 'delete invoice-down-payment',
                'group' => 'invoice-down-payment',
                'guard_name' => 'web',
            ],
            [
                'name' => 'view purchase-down-payment',
                'group' => 'purchase-down-payment',
                'guard_name' => 'web',
            ],
            [
                'name' => 'create purchase-down-payment',
                'group' => 'purchase-down-payment',
                'guard_name' => 'web',
            ],
            [
                'name' => 'edit purchase-down-payment',
                'group' => 'purchase-down-payment',
                'guard_name' => 'web',
            ],
            [
                'name' => 'delete purchase-down-payment',
                'group' => 'purchase-down-payment',
                'guard_name' => 'web',
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fund_submissions', function (Blueprint $table) {
            $table->dropColumn('tax_attachment');
        });

        Schema::table('cash_advance_payments', function (Blueprint $table) {
            $table->dropColumn('tax_attachment');
        });

        Schema::table('cash_advance_receives', function (Blueprint $table) {
            $table->dropColumn('tax_attachment');
        });
    }
}
