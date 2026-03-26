<?php

use App\Models\Division;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDivisionIdToModelAuthorizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('model_authorizations', function (Blueprint $table) {
            $table->foreignIdFor(Division::class)->after('model_id')->nullable()->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('model_authorizations', function (Blueprint $table) {
            $table->dropForeign('model_authorizations_model_id_foreign');
            $table->dropColumn('division_id');
        });
    }
}
