<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;

class AddLockStatusToInvoiceParentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice_parents', function (Blueprint $table) {
            $table->boolean('lock_status')->after('payment_status')->default(false);
        });

        Schema::table('supplier_invoice_parents', function (Blueprint $table) {
            $table->boolean('lock_status')->after('payment_status')->default(false);
        });

        Permission::create([
            'name' => "invoice-general lock",
            'group' => "invoice-general",
            'guard_name' => 'web',
        ]);

        Permission::create([
            'name' => "invoice-trading lock",
            'group' => "invoice-trading",
            'guard_name' => 'web',
        ]);

        Permission::create([
            'name' => "supplier-invoice lock",
            'group' => "supplier-invoice",
            'guard_name' => 'web',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoice_parents', function (Blueprint $table) {
            $table->dropColumn('lock_status');
        });

        Schema::table('supplier_invoice_parents', function (Blueprint $table) {
            $table->dropColumn('lock_status');
        });

        Permission::whereIn('name', [
            "invoice-general lock",
            "invoice-trading lock",
            "supplier-invoice lock"
        ])->delete();
    }
}
