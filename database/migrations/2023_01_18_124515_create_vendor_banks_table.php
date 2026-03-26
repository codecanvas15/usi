<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorBanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendor_banks', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Vendor::class)->constrained();
            $table->string('name');
            $table->string('account_number');
            $table->string('behalf_of');
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
        Schema::dropIfExists('vendor_banks');
    }
}
