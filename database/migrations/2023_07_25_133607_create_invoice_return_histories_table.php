<?php

use App\Models\InvoiceReturn;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceReturnHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_return_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(InvoiceReturn::class);
            $table->date('date')->nullable();
            $table->string('reference_model')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('reference_parent_model')->nullable();
            $table->unsignedBigInteger('reference_parent_id')->nullable();
            $table->decimal('amount', 18, 2)->nullable();
            $table->string('status')->default('pending');
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
        Schema::dropIfExists('invoice_return_histories');
    }
}
