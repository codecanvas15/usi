<?php

use App\Models\Item;
use App\Models\Quotation;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotationItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Item::class);
            $table->foreignIdFor(Quotation::class);
            $table->string('item_type');
            $table->string('type', 50);
            $table->decimal('price', 18, 3);
            $table->decimal('quantity', 18, 3);
            $table->decimal('sub_total', 18, 3);
            $table->decimal('sub_total_after_tax', 18, 3);
            $table->decimal('total', 18, 3);
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
        Schema::dropIfExists('quotation_items');
    }
}
