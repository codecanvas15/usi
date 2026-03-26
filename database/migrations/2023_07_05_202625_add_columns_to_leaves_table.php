<?php

use App\Models\Division;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToLeavesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->foreignIdFor(Division::class)->nullable()->after('employee_id')->constrained();
            $table->string('cause')->nullable()->after('division_id');
            $table->string('address')->nullable()->after('status');
            $table->string('phone_number')->nullable()->after('address');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leaves', function (Blueprint $table) {
            //
        });
    }
}
