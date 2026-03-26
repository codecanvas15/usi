<?php

use App\Models\ModelTable;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModelAuthorizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('model_authorizations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ModelTable::class, 'model_id')->constrained();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->integer('level')->default(0);
            $table->double('minimum_value')->default(0);
            $table->softDeletes();
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
        Schema::dropIfExists('model_authorizations');
    }
}
