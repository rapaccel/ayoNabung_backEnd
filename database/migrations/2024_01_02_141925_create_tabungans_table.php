<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tabungans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_user');
            $table->integer('jumlah');
            $table->string('jenis');
            $table->string('tipe');
            $table->text('keterangan');
            $table->timestamps();

            $table->foreign('id_user')->references('id')->on('users'); // membuat relasi foreign key

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tabungans');
    }
};
