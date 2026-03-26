<?php

use App\Models\Asset;
use App\Models\Branch;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepreciationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('depreciations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Branch::class)->constrained();
            $table->foreignIdFor(Asset::class)->constrained();
            $table->date('date');
            $table->decimal('amount', 18, 3);
            $table->text('note');
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
        Schema::dropIfExists('depreciations');
    }
}
