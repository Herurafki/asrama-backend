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
        //
        Schema::create('siswa_kamars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('kamar_id')->constrained('kamars')->cascadeOnDelete();
            $table->dateTime('tgl_masuk')->nullable();
            $table->dateTime('tgl_keluar')->nullable();
            $table->string('keterangan')->nullable();
            $table->timestamps();
        
            $table->index(['siswa_id','tgl_keluar']); // cepat cari aktif
            $table->index(['kamar_id','tgl_keluar']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('siswa_kamars');
    }
};
