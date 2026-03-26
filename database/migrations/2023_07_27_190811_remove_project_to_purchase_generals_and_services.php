<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveProjectToPurchaseGeneralsAndServices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::table('purchase_order_generals', function (Blueprint $table) {
        //     $table->dropForeign(['project_id']);
        //     $table->dropColumn('project_id');
        // });

        // Schema::table('purchase_order_services', function (Blueprint $table) {
        //     $table->dropForeign(['project_id']);
        //     $table->dropColumn('project_id');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('purchase_order_generals', function (Blueprint $table) {
        //     $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('cascade');
        // });

        // Schema::table('purchase_order_services', function (Blueprint $table) {
        //     $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('cascade');
        // });
    }
}
