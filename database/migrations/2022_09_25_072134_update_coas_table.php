<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCoasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('coas', function (Blueprint $table) {
            $table->foreignId('parent_id')->nullable()->after('account_type')->constrained('coas');
            $table->boolean('can_have_children')->default(false)->after('parent_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('coas', function (Blueprint $table) {
            $table->dropConstrainedForeignId('parent_id');
        });
    }
}
