<?php

use App\Models\Tax;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTaxIdToDispositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dispositions', function (Blueprint $table) {
            $table->string('code')->after('id')->nullable();
            $table->foreignIdFor(Tax::class)->nullable()->after('selling_coa_id')->constrained();
            $table->string('tax_number')->nullable()->after('tax_id');
            $table->decimal('tax_value', 18, 2)->nullable()->after('selling_price');
            $table->decimal('tax_amount', 18, 2)->nullable()->after('tax_value');
            $table->decimal('total', 18, 2)->nullable()->after('tax_amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dispositions', function (Blueprint $table) {
            $table->dropForeign('dispositions_tax_id_foreign');
            $table->dropColumn('tax_id');
            $table->dropColumn(['tax_value', 'tax_amount', 'total', 'code', 'tax_number']);
        });
    }
}
