<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewerQuotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'quotations',
            function (Blueprint $table) {
                $table->dropForeign(['item_id']);
                $table->dropForeign(['price_id']);

                $table->dropColumn([
                    'item_id',
                    'price_id',
                    'kode',
                    'harga',
                    'jumlah_barang',
                    'deleted_at',
                ]);
            }
        );

        // Schema::dropIfExists('quotations');
        Schema::table('quotations', function (Blueprint $table) {
            $table->string('code');
            $table->date('date');
            $table->decimal('sub_total', 18, 3);
            $table->decimal('additional_subtotal', 18, 3);
            $table->decimal('sub_total_after_tax', 18, 3);
            $table->decimal('additional_sub_total_after_tax', 18, 3);
            $table->decimal('addtional_total', 18, 3);
            $table->decimal('total', 18, 3);
            $table->text('information')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quotations');
    }
}
