<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceGeneralsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_generals', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\SaleOrderGeneral::class)->constrained();
            $table->foreignIdFor(\App\Models\DeliveryOrderGeneral::class)->constrained();
            $table->foreignIdFor(\App\Models\Customer::class)->constrained();
            $table->foreignIdFor(\App\Models\Currency::class)->constrained();
            $table->foreignIdFor(\App\Models\BankInternal::class)->nullable()->constrained();
            $table->decimal('exchange_rate', 18, 3);
            $table->string('code', 60);
            $table->date('date');
            $table->date('due_date');
            $table->integer('due');
            $table->decimal('sub_total_main', 18, 3)->default(0);
            $table->decimal('total_tax_main', 18, 3)->default(0);
            $table->decimal('total_main', 18, 3)->default(0);
            $table->decimal('sub_total_additional', 18, 3)->default(0);
            $table->decimal('total_tax_additional', 18, 3)->default(0);
            $table->decimal('total_additional', 18, 3)->default(0);
            $table->decimal('total', 18, 3)->default(0);
            $table->string('status', 60)->default('pending');
            $table->string('payment_status', 60)->default('pending');
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
        Schema::dropIfExists('invoice_generals');
    }
}
