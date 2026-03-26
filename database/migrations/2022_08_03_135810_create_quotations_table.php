<?php

use App\Models\Customer;
use App\Models\Item;
use App\Models\Price;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Customer::class)->constrained();
            $table->foreignIdFor(Item::class)->constrained();
            $table->foreignIdFor(Price::class)->constrained();
            $table->string('kode', 24);
            $table->integer('harga');
            $table->mediumInteger('jumlah_barang');
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
        Schema::dropIfExists('quotations');
    }
}
