<?php

use App\Models\Project;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProjectIdToTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_generals', function (Blueprint $table) {
            $table->foreignIdFor(Project::class)->nullable()->constrained();
        });

        Schema::table('purchase_order_services', function (Blueprint $table) {
            $table->foreignIdFor(Project::class)->nullable()->constrained();
        });

        Schema::table('item_receiving_reports', function (Blueprint $table) {
            $table->foreignIdFor(Project::class)->nullable()->constrained();
        });

        Schema::table('journals', function (Blueprint $table) {
            $table->foreignIdFor(Project::class)->nullable()->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_order_generals', function (Blueprint $table) {
            $table->dropConstrainedForeignId('project_id');
        });

        Schema::table('purchase_order_service', function (Blueprint $table) {
            $table->dropConstrainedForeignId('project_id');
        });

        Schema::table('item_receiving_reports', function (Blueprint $table) {
            $table->dropConstrainedForeignId('project_id');
        });

        Schema::table('journals', function (Blueprint $table) {
            $table->dropConstrainedForeignId('project_id');
        });
    }
}
