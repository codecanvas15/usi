<?php

use App\Models\Currency;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFundSubmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fund_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained('branches');
            $table->foreignId('created_by')->constrained('users');
            $table->enum('item', ['lpb','general','dp']);
            $table->string('reference')->nullable();
            $table->string('vendor');
            $table->string('alamat');
            $table->foreignIdFor(Currency::class)->constrained('currencies');
            $table->double('amount');
            $table->mediumText('keterangan');
            $table->string('status', 24);
            $table->string('reject_reason')->nullable();
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
        Schema::dropIfExists('fund_submissions');
    }
}
