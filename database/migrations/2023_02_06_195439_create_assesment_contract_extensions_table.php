<?php

use App\Models\ContractExtension;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssesmentContractExtensionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assesment_contract_extensions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ContractExtension::class)->constrained();
            $table->string('type');
            $table->string('value');
            $table->text('note');
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
        Schema::dropIfExists('assesment_contract_extensions');
    }
}
