<?php

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\FloatType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNullableCreditToSupplierInvoiceGeneralDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Type::hasType('double')) {
            Type::addType('double', FloatType::class);
        }
        Schema::table('supplier_invoice_general_details', function (Blueprint $table) {
            $table->double('credit', 18, 3)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('supplier_invoice_general_details', function (Blueprint $table) {
            $table->double('credit', 18, 3);
        });
    }
}
