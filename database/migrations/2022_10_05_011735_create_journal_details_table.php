<?php

use App\Models\Coa;
use App\Models\Journal;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJournalDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('journal_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Journal::class)->constrained();
            $table->foreignIdFor(Coa::class)->constrained();
            $table->decimal('debit', 18, 2)->nullable();
            $table->decimal('credit', 18, 2)->nullable();
            $table->string('remark')->nullable();
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
        Schema::dropIfExists('journal_details');
    }
}
