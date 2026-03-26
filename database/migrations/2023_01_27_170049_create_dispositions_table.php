<?php

use App\Models\Asset;
use App\Models\Branch;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDispositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dispositions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Branch::class)->constrained();
            $table->foreignIdFor(Asset::class)->constrained();
            $table->unsignedBigInteger('gain_loss_coa_id')->nullable();
            $table->foreign('gain_loss_coa_id')->references('id')->on('coas');
            $table->unsignedBigInteger('selling_coa_id')->nullable();
            $table->foreign('selling_coa_id')->references('id')->on('coas');
            $table->integer('is_selling_asset');
            $table->date('date');
            $table->date('last_journal_date');
            $table->decimal('last_book_value', 18, 3);
            $table->decimal('selling_price', 18, 3)->nullable();
            $table->string('location');
            $table->text('note');
            $table->string('status')->default('pending');
            $table->text('reject_reason')->nullable();
            $table->bigInteger('created_by')->nullable();
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
        Schema::dropIfExists('dispositions');
    }
}
