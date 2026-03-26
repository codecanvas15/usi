<?php

use App\Models\Asset;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAssetIdToFleetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fleets', function (Blueprint $table) {
            $table->foreignIdFor(Asset::class)->after('branch_id')->nullable()->constrained();
            $table->string('status')->after('year')->default('incomplete');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fleets', function (Blueprint $table) {
            $table->dropColumn('asset_id');
            $table->dropColumn('status');
        });
    }
}
