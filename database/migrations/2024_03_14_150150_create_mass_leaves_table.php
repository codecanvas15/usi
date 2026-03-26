<?php

use App\Models\Branch;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMassLeavesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mass_leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Branch::class)->nullable();
            $table->date('date');
            $table->date('from_date');
            $table->date('to_date');
            $table->text('necessary');
            $table->text('note');
            $table->text('attachment')->nullable();
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
        Schema::dropIfExists('mass_leaves');
    }
}
