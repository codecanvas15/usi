<?php

use App\Models\Branch;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBranchToSomeTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignIdFor(Branch::class)->after('id')->nullable()->constrained();
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->foreignIdFor(Branch::class)->after('id')->nullable()->constrained();
        });

        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->foreignIdFor(Branch::class)->after('id')->nullable()->constrained();
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->foreignIdFor(Branch::class)->after('id')->nullable()->constrained();
        });

        Schema::table('purchase_order_generals', function (Blueprint $table) {
            $table->foreignIdFor(Branch::class)->after('id')->nullable()->constrained();
        });

        Schema::table('purchase_order_services', function (Blueprint $table) {
            $table->foreignIdFor(Branch::class)->after('id')->nullable()->constrained();
        });

        Schema::table('purchase_transports', function (Blueprint $table) {
            $table->foreignIdFor(Branch::class)->after('id')->nullable()->constrained();
        });

        Schema::table('sale_orders', function (Blueprint $table) {
            $table->foreignIdFor(Branch::class)->after('id')->nullable()->constrained();
        });

        Schema::table('quotations', function (Blueprint $table) {
            $table->foreignIdFor(Branch::class)->after('id')->nullable()->constrained();
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->foreignIdFor(Branch::class)->after('id')->nullable()->constrained();
        });

        Schema::table('delivery_orders', function (Blueprint $table) {
            $table->foreignIdFor(Branch::class)->after('id')->nullable()->constrained();
        });

        Schema::table('invoice_tradings', function (Blueprint $table) {
            $table->foreignIdFor(Branch::class)->after('id')->nullable()->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
