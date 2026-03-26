<?php

use App\Models\Branch;
use App\Models\Customer;
use App\Models\ModelTable;
use App\Models\PurchaseRequestTrading;
use App\Models\ShNumber;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;

class CreatePurchaseRequestTradingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_request_tradings', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Branch::class);
            $table->foreignIdFor(Customer::class);
            $table->foreignIdFor(ShNumber::class);
            $table->string('code')->unique();
            $table->date('date');
            $table->string('status');
            $table->string('order_status');
            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users');
            $table->text('note')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Permission::insert([
            [
                'name' => 'view purchase-request-trading',
                'group' => 'purchase-request-trading',
                'guard_name' => 'web',
            ],
            [
                'name' => 'create purchase-request-trading',
                'group' => 'purchase-request-trading',
                'guard_name' => 'web',
            ],
            [
                'name' => 'edit purchase-request-trading',
                'group' => 'purchase-request-trading',
                'guard_name' => 'web',
            ],
            [
                'name' => 'delete purchase-request-trading',
                'group' => 'purchase-request-trading',
                'guard_name' => 'web',
            ]
        ]);

        ModelTable::create(
            [
                'name' => PurchaseRequestTrading::class,
                'alias' => 'Purchase Request Trading',
                'group' => 'pembelian',
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_request_tradings');
    }
}
