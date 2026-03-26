<?php

use App\Models\EduRecruitment;
use App\Models\UrlRecruitment;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecruitmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recruitments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(EduRecruitment::class)->constrained();
            $table->foreignIdFor(UrlRecruitment::class)->constrained();
            $table->string('resume');
            $table->string('foto');
            $table->string('name');
            $table->string('email');
            $table->string('no_telp')->nullable();
            $table->string('status')->nullable();
            $table->string('nama_company')->nullable();
            $table->integer('pengalaman_kerja')->nullable();
            $table->string('deskripsi_job')->nullable();
            $table->string('cover_letter')->nullable();
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
        Schema::dropIfExists('recruitments');
    }
}
