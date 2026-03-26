<?php

use App\Models\Coa;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBankCodeMutationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_code_mutations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Coa::class)->constrained()->nullable();
            $table->date('date')->nullable();
            $table->string('ref_model')->nullable();
            $table->bigInteger('ref_id')->nullable();
            $table->string('type');
            $table->string('code');
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
        Schema::dropIfExists('bank_code_mutations');
    }
}
