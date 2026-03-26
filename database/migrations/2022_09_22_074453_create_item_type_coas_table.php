<?php

use App\Models\Coa;
use App\Models\ItemType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemTypeCoasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_type_coas', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ItemType::class)->constrained();
            $table->foreignIdFor(Coa::class)->nullable()->constrained();
            $table->string('type', 60);
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
        Schema::dropIfExists('item_type_coas');
    }
}
