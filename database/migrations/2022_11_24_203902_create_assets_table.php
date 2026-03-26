<?php

use App\Models\Branch;
use App\Models\Coa;
use App\Models\Division;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\ItemReceivingReportDetail;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Branch::class)->constrained();
            $table->foreignIdFor(ItemReceivingReportDetail::class)->nullable()->constrained();
            $table->foreignIdFor(Item::class)->nullable()->constrained();
            $table->foreignIdFor(ItemCategory::class)->nullable()->constrained();
            $table->foreignIdFor(Division::class, 'division_id')->nullable()->constrained();
            $table->foreignIdFor(Coa::class, 'asset_coa_id')->nullable()->references('id')->on('coas')->constrained();
            $table->foreignIdFor(Coa::class, 'acumulated_depreciation_coa_id')->nullable()->references('id')->on('coas')->constrained();
            $table->foreignIdFor(Coa::class, 'depreciation_coa_id')->nullable()->references('id')->on('coas')->constrained();
            $table->string('code');
            $table->string('asset_name')->nullable();
            $table->date('purchase_date')->nullable();
            $table->date('usage_date')->nullable();
            $table->double('estimated_life')->nullable();
            $table->decimal('value', 18, 3)->nullable();
            $table->decimal('residual_value', 18, 3)->nullable();
            $table->string('initial_location')->nullable();
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
        Schema::dropIfExists('assets');
    }
}
