<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('NIK', 24)->unique();
            $table->string('alamat');
            $table->string('nomor_telepone', 24);
            $table->string('tempat_lahir', 100);
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['Laki-Laki', 'Perempuan']);
            $table->boolean('status_pernikahan');
            $table->string('nomor_ktp', 24)->unique();
            $table->string('bank', 50);
            $table->string('nomor_rekening', 24)->unique();
            $table->foreignIdFor(User::class)->unique()->constrained();
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
        Schema::dropIfExists('employees');
    }
}
