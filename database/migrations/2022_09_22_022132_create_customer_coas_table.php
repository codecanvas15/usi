<?php

use App\Models\Coa;
use App\Models\Customer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerCoasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_coas', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Coa::class)->nullable()->constrained();
            $table->foreignIdFor(Customer::class)->nullable()->constrained();
            $table->string('tipe', 36);
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
        Schema::dropIfExists('customer_coas');
    }
}
