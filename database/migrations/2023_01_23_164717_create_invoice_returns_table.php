<?php

use App\Models\Branch;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\Project;
use App\Models\WareHouse;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceReturnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Branch::class)->constrained();
            $table->foreignIdFor(Customer::class)->constrained();
            $table->foreignIdFor(Project::class)->nullable()->constrained();
            $table->foreignIdFor(WareHouse::class)->nullable()->constrained();
            $table->foreignIdFor(Currency::class)->nullable()->constrained();
            $table->decimal('exchange_rate', 18, 3);
            $table->string('type');
            $table->string('reference_model');
            $table->bigInteger('reference_id');
            $table->string('code')->nullable();
            $table->string('reference')->nullable();
            $table->date('date');
            $table->decimal('hpp_total', 18, 3);
            $table->decimal('subtotal', 18, 3);
            $table->decimal('tax_total', 18, 3);
            $table->decimal('total', 18, 3);
            $table->string('status');
            $table->string('reject_reason');
            $table->bigInteger('created_by');
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
        Schema::dropIfExists('invoice_returns');
    }
}
