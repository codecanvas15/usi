<?php

use App\Models\Branch;
use App\Models\Currency;
use App\Models\Project;
use App\Models\Vendor;
use App\Models\VendorCoa;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierInvoiceGeneralsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_invoice_generals', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Vendor::class)->constrained();
            $table->foreignIdFor(VendorCoa::class)->constrained();
            $table->foreignIdFor(Currency::class)->constrained();
            $table->foreignIdFor(Branch::class)->constrained();
            $table->foreignIdFor(Project::class)->nullable()->constrained();
            $table->string('code');
            $table->date('date');
            $table->double('exchange_rate', 18, 2);
            $table->decimal('debit', 18, 3);
            $table->decimal('credit', 18, 3);
            $table->enum('term_of_payment', ['cash', 'by days'])->default('cash');
            $table->integer('top_days')->nullable();
            $table->date('top_due_date')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users');
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
        Schema::dropIfExists('supplier_invoice_generals');
    }
}
