<?php

use App\Models\Currency;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleOrderGeneralsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_order_generals', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Branch::class)->constrained();
            $table->foreignIdFor(\App\Models\Customer::class)->constrained();
            $table->foreignIdFor(Currency::class)->constrained();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->date('tanggal');
            $table->string('kode');
            $table->decimal('sub_total', 18, 3);
            $table->decimal('total', 18, 3);
            $table->decimal('exchange_rate', 18, 3);
            $table->string('status', 32);
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
        Schema::dropIfExists('sale_order_generals');
    }
}
