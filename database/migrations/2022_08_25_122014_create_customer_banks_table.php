<?php

use App\Models\Bank;
use App\Models\Customer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerBanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_banks', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Customer::class)->constrained();
            $table->foreignIdFor(Bank::class)->constrained();
            $table->string('atas_nama');
            $table->string('nomor_rekening', 24);
            $table->string('cabang');
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
        Schema::dropIfExists('customer_banks');
    }
}
