<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateVendorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->string('email')->nullable()->after('nama');
            $table->string('business_phone')->after('email');
            $table->string('mobile_phone')->after('business_phone');
            $table->string('whatsapp')->after('mobile_phone');
            $table->string('fax')->nullable()->after('whatsapp');
            $table->string('website')->nullable()->after('fax');
            $table->string('nomor_rekening')->nullable()->after('website');
            $table->string('jenis_bank')->nullable()->after('nomor_rekening');
            $table->string('business_bank_name')->nullable()->after('jenis_bank');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn('email');
            $table->dropColumn('business_phone');
            $table->dropColumn('mobile_phone');
            $table->dropColumn('whatsapp');
            $table->dropColumn('fax');
            $table->dropColumn('website');
            $table->dropColumn('nomor_rekening');
            $table->dropColumn('jenis_bank');
            $table->dropColumn('business_bank_name');
        });
    }
}
