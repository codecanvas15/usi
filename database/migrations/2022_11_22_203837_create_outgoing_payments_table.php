<?php

use App\Models\Branch;
use App\Models\Coa;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOutgoingPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('outgoing_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Branch::class);
            $table->foreignIdFor(Coa::class);
            $table->string('code', 25);
            $table->date('date');
            $table->decimal('total', 18, 3);
            $table->string('status', 25);
            $table->foreignIdFor(User::class, 'created_by');
            $table->softDeletes();
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
        Schema::dropIfExists('outgoing_payments');
    }
}
