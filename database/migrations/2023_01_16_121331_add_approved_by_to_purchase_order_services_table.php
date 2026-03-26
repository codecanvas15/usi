<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApprovedByToPurchaseOrderServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_services', function (Blueprint $table) {
            $table->foreignId('approved_by')->nullable()->constrained('users')->after('project_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_order_services', function (Blueprint $table) {
            $table->dropConstrainedForeignId('approved_by');
        });
    }
}
