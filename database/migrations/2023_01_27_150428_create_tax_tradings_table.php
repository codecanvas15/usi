<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaxTradingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tax_tradings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('value', 18, 4);
            $table->foreignId('coa_sale_id')->nullable()->constrained('coas');
            $table->foreignId('coa_purchase_id')->nullable()->constrained('coas');
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
        Schema::dropIfExists('tax_tradings');
    }
}
