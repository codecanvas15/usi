<?php

use App\Models\Coa;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashbookDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cashbook_details', function (Blueprint $table) {
            $table->id();
            // $table->foreignIdFor(Cashbook::class);
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
        Schema::dropIfExists('cashbook_details');
    }
}
