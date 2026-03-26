<?php

use App\Models\CashAdvanceReceive;
use App\Models\Coa;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashAdvanceReceiveDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cash_advance_receive_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(CashAdvanceReceive::class);
            $table->foreignIdFor(Coa::class)->constrained();
            $table->string('type');
            $table->text('note');
            $table->decimal('debit', 18, 3);
            $table->decimal('credit', 18, 3);
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
        Schema::dropIfExists('cash_advance_receive_details');
    }
}
