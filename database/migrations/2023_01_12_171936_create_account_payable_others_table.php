<?php

use App\Models\AccountPayable;
use App\Models\Coa;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountPayableOthersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_payable_others', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(AccountPayable::class);
            $table->foreignIdFor(Coa::class)->constrained();
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
        Schema::dropIfExists('account_payable_others');
    }
}
