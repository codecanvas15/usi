<?php

use App\Models\Coa;
use App\Models\Currency;
use App\Models\Employee;
use App\Models\ItemReceivingReport;
use App\Models\Project;
use App\Models\Vendor;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepositPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deposit_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Project::class)->nullable()->constrained();
            $table->foreignIdFor(Currency::class)->constrained();
            $table->foreignIdFor(Employee::class)->nullable()->constrained();
            $table->foreignIdFor(Vendor::class)->nullable()->constrained();
            $table->foreignIdFor(ItemReceivingReport::class)->constrained();
            $table->foreignIdFor(Coa::class)->nullable()->constrained();
            $table->enum('type', ['karyawan','vendor']);
            $table->decimal('amount', 20, 2);
            $table->decimal('exchange_rate', 20, 2);
            $table->integer('top_days')->nullable();
            $table->enum('term_of_payment', ['cash', 'by days']);
            $table->date('tanggal');
            $table->mediumText('keterangan');
            $table->string('status')->default('pending');
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
        Schema::dropIfExists('deposit_payments');
    }
}
