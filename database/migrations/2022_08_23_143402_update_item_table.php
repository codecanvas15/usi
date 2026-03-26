<?php

use App\Models\ItemCategory;
use App\Models\ItemType;
use App\Models\Unit;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->string('kode', 24)->unique()->nullable()->after('id');
            $table->string('model')->nullable()->after('nama');
            $table->string('reorder_stock')->nullable()->after('deskripsi');
            $table->string('manufactur')->nullable()->after('reorder_stock');
            $table->string('status', 50)->nullable()->after('manufactur');

            $table->foreignIdFor(ItemType::class)->nullable()->after('status')->constrained();
            $table->foreignIdFor(Unit::class)->nullable()->after('item_type_id')->constrained();
            $table->foreignIdFor(ItemCategory::class)->nullable()->after('item_type_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('kode');
            $table->dropColumn('model');
            $table->dropColumn('reorder_stock');
            $table->dropColumn('manufactur');
            $table->dropColumn('status');

            $table->dropConstrainedForeignId('item_type_id');
            $table->dropConstrainedForeignId('unit_id');
            $table->dropConstrainedForeignId('item_category_id');
        });
    }
}
