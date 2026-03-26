<?php

use App\Models\Branch;
use App\Models\Currency;
use App\Models\Purchase;
use App\Models\Vendor;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseDownPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_down_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Branch::class);
            $table->foreignIdFor(Vendor::class);
            $table->foreignIdFor(Currency::class);
            $table->decimal('exchange_rate', 18, 2);
            $table->foreignIdFor(Purchase::class);
            $table->date('date');
            $table->date('due_date');
            $table->string('code')->unique();
            $table->decimal('total_amount', 18, 2);
            $table->decimal('down_payment', 18, 2);
            $table->decimal('tax_total', 18, 2);
            $table->decimal('grand_total', 18, 2);
            $table->string('payment_status')->default('unpaid');
            $table->string('status')->default('pending');
            $table->string('note')->nullable();
            $table->string('tax_number')->nullable();
            $table->text('tax_attachment')->nullable();
            $table->foreignId('created_by')->references('id')->on('users');
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
        Schema::dropIfExists('purchase_down_payments');
    }
}
