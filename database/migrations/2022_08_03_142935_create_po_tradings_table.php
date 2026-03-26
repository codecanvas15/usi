<?php

use App\Models\Customer;
use App\Models\ShNumber;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePoTradingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('po_tradings', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Customer::class)->constrained();
            $table->foreignIdFor(ShNumber::class)->constrained();
            $table->date('tanggal');
            $table->string('nomor_po', 24)->nullable()->unique();
            $table->integer('jumlah')->nullable();
            $table->integer('sub_total')->nullable();
            $table->string('add_tax')->nullable();
            $table->integer('total')->nullable();
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
        Schema::dropIfExists('po_tradings');
    }
}
