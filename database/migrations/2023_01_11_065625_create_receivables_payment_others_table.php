<?php

use App\Models\Coa;
use App\Models\ReceivablesPayment;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReceivablesPaymentOthersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receivables_payment_others', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ReceivablesPayment::class);
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
        Schema::dropIfExists('receivables_payment_others');
    }
}
