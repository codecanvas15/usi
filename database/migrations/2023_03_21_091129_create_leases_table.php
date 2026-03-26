<?php

use App\Models\Branch;
use App\Models\Coa;
use App\Models\Division;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leases', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Branch::class)->constrained();
            $table->foreignIdFor(Division::class, 'division_id')->nullable()->constrained();
            $table->foreignIdFor(Coa::class, 'asset_coa_id')->nullable()->references('id')->on('coas')->constrained();
            $table->foreignIdFor(Coa::class, 'acumulated_depreciation_coa_id')->nullable()->references('id')->on('coas')->constrained();
            $table->foreignIdFor(Coa::class, 'depreciation_coa_id')->nullable()->references('id')->on('coas')->constrained();
            $table->string('code');
            $table->string('lease_name')->nullable();
            $table->date('date')->nullable();
            $table->date('from_date')->nullable();
            $table->date('to_date')->nullable();
            $table->double('month_duration')->nullable();
            $table->decimal('value', 18, 3)->nullable();
            $table->decimal('depreciation_value', 18, 3)->nullable();
            $table->string('note')->nullable();
            $table->string('status');
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
        Schema::dropIfExists('leases');
    }
}
