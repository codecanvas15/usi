<?php

use App\Models\Branch;
use App\Models\ModelAuthorization;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModelAuthorizationBranchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('model_authorization_branches', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ModelAuthorization::class)->constrained();
            $table->foreignIdFor(Branch::class)->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('model_authorization_branches');
    }
}
