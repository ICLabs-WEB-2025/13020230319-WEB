<?php

  use Illuminate\Database\Migrations\Migration;
  use Illuminate\Database\Schema\Blueprint;
  use Illuminate\Support\Facades\Schema;

  class CreateSimsTable extends Migration
  {
      public function up()
      {
          Schema::create('sims', function (Blueprint $table) {
              $table->id('sim_id');
              $table->unsignedBigInteger('user_id');
              $table->string('nomor_sim')->unique();
              $table->string('nama');
              $table->string('tempat_lahir');
              $table->date('tanggal_lahir');
              $table->enum('jenis_kelamin', ['L', 'P']);
              $table->text('alamat');
              $table->string('pekerjaan')->nullable();
              $table->string('jenis_sim');
              $table->string('nomor_ktp')->unique();
              $table->date('tanggal_penerbitan');
              $table->date('masa_berlaku');
              $table->string('status')->default('Aktif');
              $table->timestamps();

              $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
          });
      }

      public function down()
      {
          Schema::dropIfExists('sims');
      }
  }