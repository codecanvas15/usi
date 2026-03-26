<?php

use App\Models\Bank;
use App\Models\Branch;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBankInternalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_internals', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Bank::class)->constrained();
            $table->foreignIdFor(Branch::class)->constrained();
            $table->string('no_rekening', 60);
            $table->string('on_behalf_of', 60);
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
        Schema::dropIfExists('bank_internals');
    }
}
