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
        Schema::create('perizinans', function (Blueprint $table) {
            $table->id();
            $table->text('alasan');
            $table->string('tanggal_keluar')->nullable();
            $table->string('tanggal_masuk')->nullable();
            $table->time('waktu_keluar')->nullable();
            $table->time('waktu_masuk')->nullable();
            $table->enum('status', ['Menunggu', 'Ditolak', 'Diterima']);
            $table->text('keterangan')->nullable();
            $table->foreignId('siswa_id')->constrained()->cascadeOnDelete();
            $table->foreignId('users_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perizinans');
    }
};
