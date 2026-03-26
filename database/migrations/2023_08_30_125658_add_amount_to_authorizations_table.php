<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAmountToAuthorizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('authorizations', function (Blueprint $table) {
            $table->decimal('amount', 18, 2)->after('update_status_link')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('authorizations', function (Blueprint $table) {
            $table->dropColumn('amount');
        });
    }
}
