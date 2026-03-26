<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class updateCustomersTableTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('no_pic_customer');
            $table->string('npwp')->nullable()->change();

            $table->string('email')->nullable()->unique()->after('npwp');
            $table->string('bussiness_phone', 24)->nullable()->unique()->after('email');
            $table->string('mobile_phone', 24)->nullable()->unique()->after('bussiness_phone');
            $table->string('fax', 24)->nullable()->unique()->after('mobile_phone');
            $table->string('website')->nullable()->unique()->after('fax');
            $table->string('whatsapp_number')->nullable()->unique()->after('fax');
            $table->decimal('lost_tolerance', 18, 2)->nullable()->after('whatsapp_number');
            $table->string('lost_tolerance_type', 24)->nullable()->after('lost_tolerance');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
