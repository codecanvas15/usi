<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNonEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('non_employees', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->nullable();
            $table->string('name');
            $table->enum('gender', ['Laki-laki', 'Perempuan']);
            $table->string('phone');
            $table->string('agency');
            $table->text('address');
            $table->string('identity_number');
            $table->string('role');
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
        Schema::dropIfExists('non_employees');
    }
}
