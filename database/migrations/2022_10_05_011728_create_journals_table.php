<?php

use App\Models\Currency;
use App\Models\JournalType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJournalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('journals', function (Blueprint $table) {
            $table->id();
            $table->string('code', 24)->unique();
            $table->date('date');
            $table->string('reference')->nullable();
            $table->text('remark')->nullable();
            $table->decimal('exchange_rate', 18, 2);
            $table->string('status', 24)->default('pending');
            $table->decimal('credit_total', 18, 2)->nullable();
            $table->decimal('debit_total', 18, 2)->nullable();
            $table->foreignIdFor(JournalType::class)->constrained();
            $table->foreignIdFor(Currency::class)->constrained();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
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
        Schema::dropIfExists('journals');
    }
}
