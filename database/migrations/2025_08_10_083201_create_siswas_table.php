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
        Schema::create('siswas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lengkap');
            $table->string('nama_panggilan');
            $table->string('nis')->nullable();
            $table->string('tempat_lahir');
            $table->string('tanggal_lahir');
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan']);
            $table->enum('kewarganegaraan', ['WNI', 'WNA']);
            $table->enum('status_keluarga', ['Kandung', 'Angkat']);
            $table->enum('status_orangtua', ['Yatim', 'Piatu','Yatim piatu', 'Dhuafa'])->nullable();
            $table->string('anak_ke');
            $table->string('tgl_masuk');
            $table->string('kelas');
            $table->foreignId('kamar_id')->nullable()->constrained('kamars')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('foto')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswas');
    }
};
