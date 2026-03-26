<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropIndexCustomers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            // $table->dropUnique([
            //     'customers_bussiness_phone_unique',
            //     'customers_fax_unique',
            //     'customers_whatsapp_number_unique',
            //     'customers_npwp_unique',
            //     'customers_email_unique',
            //     'customers_mobile_phone_unique',
            //     'customers_website_unique',
            //     ]);
            $table->dropIndex('customers_bussiness_phone_unique');
            $table->dropIndex('customers_fax_unique');
            $table->dropIndex('customers_whatsapp_number_unique');
            $table->dropIndex('customers_npwp_unique');
            $table->dropIndex('customers_email_unique');
            $table->dropIndex('customers_mobile_phone_unique');
            $table->dropIndex('customers_website_unique');
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
