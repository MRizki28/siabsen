<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_absen', function (Blueprint $table) {
            $table->id();
            $table->string('uuid');
            $table->foreignId('id_user')->constrained('users');
            $table->string('tanggal');
            $table->string('waktu');
            $table->enum('status' ,['hadir', 'lambat' , 'tidak hadir'])->default('tidak hadir');
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
        Schema::dropIfExists('tb_absen');
    }
};
