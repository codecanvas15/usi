<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeInPurchaseRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->enum('type', ["trading", "general", "jasa", "transportir"])->default('general')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
