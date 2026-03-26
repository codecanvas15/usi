<?php

use App\Models\Branch;
use App\Models\Currency;
use App\Models\Customer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceParentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_parents', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Branch::class)->constrained();
            $table->foreignIdFor(Customer::class)->constrained();
            $table->foreignIdFor(Currency::class)->constrained();
            $table->decimal('exchange_rate', 18, 3);
            $table->date('date');
            $table->date('due_date');
            $table->string('model_reference');
            $table->bigInteger('reference_id');
            $table->string('type');
            $table->string('code');
            $table->decimal('total', 18, 3);
            $table->string('status');
            $table->string('payment_status');
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
        Schema::dropIfExists('invoice_parents');
    }
}
