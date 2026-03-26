<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSomeColumnToActivityStatusLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('activity_status_logs', function (Blueprint $table) {
            $table->string('from_status', 34)->after('message');
            $table->string('to_status', 34)->after('from_status');
            $table->foreignIdFor(User::class)->after('to_status')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('activity_status_logs', function (Blueprint $table) {
            //
        });
    }
}
