<?php

use App\Models\Division;
use App\Models\ModelAuthorization;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModelAuthorizationDivisionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('model_authorization_divisions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ModelAuthorization::class)->constrained();
            $table->foreignIdFor(Division::class)->constrained();
            $table->timestamps();
        });

        $modelAuthorizations = ModelAuthorization::all();
        foreach ($modelAuthorizations as $modelAuthorization) {
            if ($modelAuthorization->division_id) {
                $modelAuthorization->model_authorization_divisions()->create([
                    'model_authorization_id' => $modelAuthorization->id,
                    'division_id' => $modelAuthorization->division_id
                ]);
            }
        }

        Schema::table('model_authorizations', function (Blueprint $table) {
            $table->dropForeign('model_authorizations_division_id_foreign');
            $table->dropColumn('division_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('model_authorization_divisions');
    }
}
