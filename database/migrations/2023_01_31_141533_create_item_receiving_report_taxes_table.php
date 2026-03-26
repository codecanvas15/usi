<?php

use App\Models\Tax;
use App\Models\Vendor;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemReceivingReportTaxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_receiving_report_taxes', function (Blueprint $table) {
            $table->id();
            $table->string('reference_model');
            $table->bigInteger('reference_id');
            $table->string('reference_parent_model');
            $table->bigInteger('reference_parent_id');
            $table->date('date');
            $table->foreignIdFor(Vendor::class)->nullable()->constrained();
            $table->foreignIdFor(Tax::class)->constrained();
            $table->decimal('dpp', 18, 3);
            $table->decimal('value', 18, 3);
            $table->decimal('amount', 18, 3);
            $table->string('status')->default('pending');
            $table->decimal('outstanding', 18, 3);
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
        Schema::dropIfExists('item_receiving_report_taxes');
    }
}
