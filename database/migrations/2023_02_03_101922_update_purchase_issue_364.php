<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePurchaseIssue364 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('purchase_order_service_detail_taxes');
        Schema::dropIfExists('purchase_order_service_details');
        Schema::dropIfExists('purchase_order_services');

        Schema::dropIfExists('purchase_order_general_detail_taxes');
        Schema::dropIfExists('purchase_order_general_details');
        Schema::dropIfExists('purchase_order_generals');

        Schema::create('purchase_order_generals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('vendor_id');
            $table->unsignedBigInteger('project_id')->nullable();
            $table->unsignedBigInteger('currency_id')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();

            $table->string('code');
            $table->date('date');
            $table->string('status');
            $table->string('quotation')->nullable();
            $table->string('term_of_payment', 30);
            $table->string('term_of_payment_days', 30);

            $table->decimal('exchange_rate', 18, 2)->default(1);
            $table->decimal('total', 18, 2)->default(0);
            $table->decimal('total_main', 18, 2)->default(0);
            $table->decimal('total_additional', 18, 2)->default(0);
            $table->decimal('total_tax_main', 18, 2)->default(0);
            $table->decimal('total_tax_additional', 18, 2)->default(0);
            $table->decimal('amount_discount', 18, 2)->default(0);

            $table->softDeletes();
            $table->timestamps();

            $table->foreign('purchase_id', "po_general_to_purchase_parent")->references('id')->on('purchases');
            $table->foreign('branch_id', "po_general_to_branch")->references('id')->on('branches');
            $table->foreign('vendor_id', "po_general_to_vendor")->references('id')->on('vendors');
            $table->foreign('project_id', "po_general_to_project")->references('id')->on('projects');
            $table->foreign('currency_id', "po_general_to_currency")->references('id')->on('currencies');

            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('approved_by')->references('id')->on('users');
        });

        Schema::create('purchase_order_general_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_order_general_id');
            $table->unsignedBigInteger('purchase_request_id')->nullable();

            $table->string('type', 20)->default('main');
            $table->string('status');

            $table->decimal('sub_total', 18, 2)->default(0);
            $table->decimal('sub_total_after_tax', 18, 2)->default(0);
            $table->decimal('amount_discount', 18, 2)->default(0);
            $table->decimal('tax_total', 18, 2)->default(0);
            $table->decimal('total', 18, 2)->default(0);

            $table->timestamps();

            $table->foreign('purchase_order_general_id', "po_general_detail_to_parent")->references('id')->on('purchase_order_generals');
            $table->foreign('purchase_request_id', "po_general_detail_to_pr")->references('id')->on('purchase_requests');
        });

        Schema::create('purchase_order_general_detail_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_order_general_detail_id');
            $table->unsignedBigInteger('purchase_request_detail_id')->nullable();
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('unit_id');

            $table->string('status');

            $table->decimal('quantity', 18, 2)->default(0);
            $table->decimal('quantity_received', 18, 2)->default(0);
            $table->decimal('price', 18, 2)->default(0);

            $table->string('discount_type', 20)->default('percent');
            $table->decimal('discount_value', 18, 2)->default(0);
            $table->decimal('discount_value_percent', 18, 4)->default(0);

            $table->decimal('sub_total', 18, 2)->default(0);
            $table->decimal('sub_total_after_tax', 18, 2)->default(0);
            $table->decimal('amount_discount', 18, 2)->default(0);
            $table->decimal('tax_total', 18, 2)->default(0);
            $table->decimal('total', 18, 2)->default(0);

            $table->timestamps();

            $table->foreign('purchase_order_general_detail_id', "po_gen_item_to_po_gen_detail")->references('id')->on('purchase_order_general_details');
            $table->foreign('purchase_request_detail_id', "po_gen_item_to_pr_detail")->references('id')->on('purchase_request_details');
            $table->foreign('item_id', "po_gen_to_item")->references('id')->on('items');
            $table->foreign('unit_id', "po_gen_to_unit")->references('id')->on('units');
        });

        Schema::create('purchase_order_general_detail_item_taxes', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('purchase_order_general_detail_item_id');
            $table->unsignedBigInteger('tax_id');

            $table->decimal('value', 18, 4)->default(0);
            $table->decimal('total', 18, 2)->default(0);

            $table->timestamps();

            $table->foreign('purchase_order_general_detail_item_id', "po_gen_item_tax_to_parent")->references('id')->on('purchase_order_general_detail_items');
            $table->foreign('tax_id', "po_gen_item_tax_to_tax")->references('id')->on('taxes');
        });

        // ! ------------------------------------------------------------------------------------------------------------------------------------------------

        Schema::create('purchase_order_services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('vendor_id');
            $table->unsignedBigInteger('project_id')->nullable();
            $table->unsignedBigInteger('currency_id')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();

            $table->string('code');
            $table->date('date');
            $table->string('status');
            $table->string('quotation')->nullable();
            $table->string('term_of_payment', 30);
            $table->string('term_of_payment_days', 30);

            $table->decimal('exchange_rate', 18, 2)->default(1);
            $table->decimal('total', 18, 2)->default(0);
            $table->decimal('total_main', 18, 2)->default(0);
            $table->decimal('total_additional', 18, 2)->default(0);
            $table->decimal('total_tax_main', 18, 2)->default(0);
            $table->decimal('total_tax_additional', 18, 2)->default(0);
            $table->decimal('amount_discount', 18, 2)->default(0);

            $table->softDeletes();
            $table->timestamps();

            $table->foreign('purchase_id', "po_service_to_purchase_parent")->references('id')->on('purchases');
            $table->foreign('branch_id', "po_service_to_branch")->references('id')->on('branches');
            $table->foreign('vendor_id', "po_service_to_vendor")->references('id')->on('vendors');
            $table->foreign('project_id', "po_service_to_project")->references('id')->on('projects');
            $table->foreign('currency_id', "po_service_to_currency")->references('id')->on('currencies');

            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('approved_by')->references('id')->on('users');
        });

        Schema::create('purchase_order_service_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_order_service_id');
            $table->unsignedBigInteger('purchase_request_id')->nullable();

            $table->string('type', 20)->default('main');
            $table->string('status');

            $table->decimal('sub_total', 18, 2)->default(0);
            $table->decimal('sub_total_after_tax', 18, 2)->default(0);
            $table->decimal('amount_discount', 18, 2)->default(0);
            $table->decimal('tax_total', 18, 2)->default(0);
            $table->decimal('total', 18, 2)->default(0);

            $table->timestamps();

            $table->foreign('purchase_order_service_id', "po_service_detail_to_parent")->references('id')->on('purchase_order_services');
            $table->foreign('purchase_request_id', "po_service_detail_to_pr")->references('id')->on('purchase_requests');
        });

        Schema::create('purchase_order_service_detail_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_order_service_detail_id');
            $table->unsignedBigInteger('purchase_request_detail_id')->nullable();
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('unit_id');

            $table->string('status');

            $table->decimal('quantity', 18, 2)->default(0);
            $table->decimal('quantity_received', 18, 2)->default(0);
            $table->decimal('price', 18, 2)->default(0);

            $table->string('discount_type', 20)->default('percent');
            $table->decimal('discount_value', 18, 2)->default(0);
            $table->decimal('discount_value_percent', 18, 4)->default(0);

            $table->decimal('sub_total', 18, 2)->default(0);
            $table->decimal('sub_total_after_tax', 18, 2)->default(0);
            $table->decimal('amount_discount', 18, 2)->default(0);
            $table->decimal('tax_total', 18, 2)->default(0);
            $table->decimal('total', 18, 2)->default(0);

            $table->timestamps();

            $table->foreign('purchase_order_service_detail_id', "po_ser_item_to_po_gen_detail")->references('id')->on('purchase_order_service_details');
            $table->foreign('purchase_request_detail_id', "po_ser_item_to_pr_detail")->references('id')->on('purchase_request_details');
            $table->foreign('item_id', "po_ser_to_item")->references('id')->on('items');
            $table->foreign('unit_id', "po_ser_to_unit")->references('id')->on('units');
        });

        Schema::create('purchase_order_service_detail_item_taxes', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('purchase_order_service_detail_item_id');
            $table->unsignedBigInteger('tax_id');

            $table->decimal('value', 18, 4)->default(0);
            $table->decimal('total', 18, 2)->default(0);

            $table->timestamps();

            $table->foreign('purchase_order_service_detail_item_id', "po_ser_item_tax_to_parent")->references('id')->on('purchase_order_service_detail_items');
            $table->foreign('tax_id', "po_ser_item_tax_to_tax")->references('id')->on('taxes');
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
