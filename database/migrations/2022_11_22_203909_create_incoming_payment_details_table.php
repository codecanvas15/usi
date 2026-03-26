<?php

use App\Models\Coa;
use App\Models\IncomingPayment;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncomingPaymentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incoming_payment_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(IncomingPayment::class)->constrained();
            $table->foreignIdFor(Coa::class);
            $table->decimal('amount', 18, 3);
            $table->string('note');
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
        Schema::dropIfExists('incoming_payment_details');
    }
}
