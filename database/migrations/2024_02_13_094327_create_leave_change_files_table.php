<?php

use App\Models\Leave;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeaveChangeFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leave_change_files', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Leave::class)->constrained()->onDelete('cascade');
            $table->text('file_path');
            $table->text('file_name')->nullable();
            $table->enum('status', ['approved', 'rejected', 'pending'])->default('pending');
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
        Schema::dropIfExists('leave_change_files');
    }
}
