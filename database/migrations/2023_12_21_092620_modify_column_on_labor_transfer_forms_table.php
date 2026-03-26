<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyColumnOnLaborTransferFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('labor_transfer_forms', function (Blueprint $table) {
            $table->dropForeign('labor_transfer_forms_created_by_foreign');
            $table->dropForeign('labor_transfer_forms_approved_by_foreign');
        });

        Schema::table('labor_transfer_forms', function (Blueprint $table) {
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('approved_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('labor_transfer_forms', function (Blueprint $table) {
            $table->dropForeign('labor_transfer_forms_created_by_foreign');
            $table->dropForeign('labor_transfer_forms_approved_by_foreign');
        });
    }
}
